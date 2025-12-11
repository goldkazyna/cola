<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Services\DrawingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReceiptController extends Controller
{
    // Список чеков
    public function index(Request $request)
    {
        $query = Receipt::with('user')->orderBy('created_at', 'desc');

        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Фильтр по телефону
        if ($request->filled('phone')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->phone . '%');
            });
        }

        // Фильтр по периоду
        if ($request->filled('period')) {
            $drawingService = new DrawingService();
            $periods = $drawingService->getAllPeriods();
            
            foreach ($periods as $period) {
                if ($period['drawing_date'] === $request->period) {
                    $query->whereBetween('created_at', [
                        $period['start'] . ' 00:00:00',
                        $period['end'] . ' 23:59:59',
                    ]);
                    break;
                }
            }
        }

        $receipts = $query->paginate(20);
        
        $drawingService = new DrawingService();
        $periods = $drawingService->getAllPeriods();

        return view('admin.receipts.index', compact('receipts', 'periods'));
    }

    // Отклонить чек
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $receipt = Receipt::findOrFail($id);
        $receipt->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason ?? 'Не соответствует условиям акции',
            'moderated_at' => now(),
        ]);

        return back()->with('success', 'Чек отклонён');
    }

    // Одобрить чек
    public function approve($id)
    {
        $receipt = Receipt::findOrFail($id);
        $receipt->update([
            'status' => 'approved',
            'reject_reason' => null,
            'moderated_at' => now(),
        ]);

        return back()->with('success', 'Чек одобрен');
    }

    // Удалить чек
    public function delete($id)
    {
        $receipt = Receipt::findOrFail($id);
        Storage::disk('public')->delete($receipt->image_path);
        $receipt->delete();

        return back()->with('success', 'Чек удалён');
    }

    // Выгрузка в Excel
	public function export(Request $request)
	{
		$query = Receipt::with('user');
		
		// Фильтр по статусу
		if ($request->boolean('approved_only', true)) {
			$query->where('status', 'approved');
		}
		
		// Фильтр по датам
		if ($request->period_type === 'dates') {
			if ($request->filled('date_from')) {
				$query->whereDate('created_at', '>=', $request->date_from);
			}
			if ($request->filled('date_to')) {
				$query->whereDate('created_at', '<=', $request->date_to);
			}
		}

		$receipts = $query->orderBy('created_at', 'desc')->get();

		// Формируем CSV
		$filename = 'receipts_' . date('Y-m-d_H-i-s') . '.csv';
		
		$headers = [
			'Content-Type' => 'text/csv; charset=UTF-8',
			'Content-Disposition' => 'attachment; filename="' . $filename . '"',
		];

		$callback = function () use ($receipts) {
			$file = fopen('php://output', 'w');
			
			// BOM для Excel
			fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
			
			// Заголовки
			fputcsv($file, ['ID', 'Телефон', 'Имя', 'Город', 'Дата загрузки', 'Статус'], ';');

			foreach ($receipts as $receipt) {
				fputcsv($file, [
					$receipt->id,
					$receipt->user->phone,
					$receipt->user->name ?? '',
					$receipt->user->city ?? '',
					$receipt->created_at->format('d.m.Y H:i'),
					$receipt->status === 'approved' ? 'Одобрен' : ($receipt->status === 'rejected' ? 'Отклонён' : 'На проверке'),
				], ';');
			}

			fclose($file);
		};

		return response()->stream($callback, 200, $headers);
	}
}