<?php

namespace App\Services;

use Carbon\Carbon;

class DrawingService
{
	// Тестовая дата (null = реальная дата)
	private ?string $testDate = '2025-12-13'; // Для тестирования ставим 10 декабря

	// Получить текущую дату (реальную или тестовую)
	private function now(): Carbon
	{
		if ($this->testDate) {
			return Carbon::parse($this->testDate);
		}
		return Carbon::now();
	}
    // Периоды розыгрышей
    private array $periods = [
        [
            'start' => '2025-12-08',
            'end' => '2025-12-14',
            'drawing_date' => '2025-12-15',
            'name' => 'Розыгрыш 15 декабря',
        ],
        [
            'start' => '2025-12-15',
            'end' => '2025-12-21',
            'drawing_date' => '2025-12-22',
            'name' => 'Розыгрыш 22 декабря',
        ],
        [
            'start' => '2025-12-22',
            'end' => '2025-12-28',
            'drawing_date' => '2025-12-29',
            'name' => 'Розыгрыш 29 декабря',
        ],
        [
            'start' => '2025-12-29',
            'end' => '2026-01-04',
            'drawing_date' => '2026-01-05',
            'name' => 'Розыгрыш 5 января',
        ],
        [
            'start' => '2026-01-05',
            'end' => '2026-01-11',
            'drawing_date' => '2026-01-12',
            'name' => 'Розыгрыш 12 января',
        ],
    ];

    // Финальный розыгрыш
    private array $finalDrawing = [
        'start' => '2025-12-08',
        'end' => '2026-01-11',
        'drawing_date' => '2026-01-12',
        'name' => 'Финальный розыгрыш 12 января',
    ];

    // Получить период для чека по дате загрузки
	public function getPeriodForReceipt(Carbon $uploadDate): ?array
	{
		foreach ($this->periods as $period) {
			$start = Carbon::parse($period['start'])->startOfDay();
			$end = Carbon::parse($period['end'])->endOfDay();

			if ($uploadDate->between($start, $end)) {
				return $period;
			}
		}

		// Если чек загружен до начала акции — относим к первому периоду
		$firstPeriodStart = Carbon::parse($this->periods[0]['start'])->startOfDay();
		if ($uploadDate->isBefore($firstPeriodStart)) {
			return $this->periods[0];
		}

		return null;
	}

    // Проверить, прошёл ли розыгрыш для периода
    public function isDrawingPassed(array $period): bool
    {
        $drawingDate = Carbon::parse($period['drawing_date'])->endOfDay();
        return $this->now()->isAfter($drawingDate);
    }

    // Получить статус чека
    public function getReceiptStatus(Carbon $uploadDate): array
    {
        $period = $this->getPeriodForReceipt($uploadDate);
        $now = $this->now();

        if (!$period) {
            return [
                'status' => 'outside',
                'message' => 'Чек загружен вне периода акции',
                'participates_weekly' => false,
                'participates_final' => false,
            ];
        }

        $drawingDate = Carbon::parse($period['drawing_date']);
        $isPassed = $this->isDrawingPassed($period);

        // Финальный розыгрыш
        $finalDate = Carbon::parse($this->finalDrawing['drawing_date']);
        $finalPassed = $now->isAfter($finalDate->endOfDay());

        if ($isPassed && $finalPassed) {
            return [
                'status' => 'completed',
                'message' => 'Все розыгрыши завершены',
                'weekly_drawing' => $period['name'],
                'participates_weekly' => true,
                'participates_final' => true,
            ];
        }

        if ($isPassed) {
            return [
                'status' => 'weekly_passed',
                'message' => 'Еженедельный розыгрыш прошёл',
                'weekly_drawing' => $period['name'],
                'next_drawing' => 'Финальный розыгрыш 12 января',
                'next_drawing_date' => '2026-01-12',
                'participates_weekly' => true,
                'participates_final' => true,
            ];
        }

        return [
            'status' => 'active',
            'message' => 'Участвует в розыгрыше',
            'weekly_drawing' => $period['name'],
            'drawing_date' => $period['drawing_date'],
            'drawing_date_formatted' => $drawingDate->format('d.m.Y'),
            'days_left' => (int) $now->startOfDay()->diffInDays($drawingDate->startOfDay()),
            'participates_weekly' => true,
            'participates_final' => true,
        ];
    }

    // Получить ближайший розыгрыш
    public function getNextDrawing(): ?array
    {
        $now = $this->now();

        foreach ($this->periods as $period) {
            $drawingDate = Carbon::parse($period['drawing_date']);
            if ($now->isBefore($drawingDate->endOfDay())) {
                return [
                    'name' => $period['name'],
                    'date' => $period['drawing_date'],
                    'date_formatted' => $drawingDate->format('d.m.Y'),
                    'days_left' => (int) $now->startOfDay()->diffInDays($drawingDate->startOfDay()),
                ];
            }
        }

        // Если все еженедельные прошли, возвращаем финальный
        $finalDate = Carbon::parse($this->finalDrawing['drawing_date']);
        if ($now->isBefore($finalDate->endOfDay())) {
            return [
                'name' => $this->finalDrawing['name'],
                'date' => $this->finalDrawing['drawing_date'],
                'date_formatted' => $finalDate->format('d.m.Y'),
                'days_left' => $now->diffInDays($finalDate, false),
            ];
        }

        return null;
    }

    // Группировка чеков по периодам
    public function groupReceiptsByPeriod(iterable $receipts): array
    {
        $grouped = [];

        foreach ($receipts as $receipt) {
            $uploadDate = Carbon::parse($receipt->created_at);
            $period = $this->getPeriodForReceipt($uploadDate);

            if ($period) {
                $key = $period['drawing_date'];
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'period' => $period,
                        'is_passed' => $this->isDrawingPassed($period),
                        'receipts' => [],
                    ];
                }
                $grouped[$key]['receipts'][] = $receipt;
            }
        }

        // Сортируем по дате розыгрыша (ближайшие сверху)
        ksort($grouped);

        return $grouped;
    }

    // Все периоды
    public function getAllPeriods(): array
    {
        return $this->periods;
    }

    // Финальный розыгрыш
    public function getFinalDrawing(): array
    {
        return $this->finalDrawing;
    }
}