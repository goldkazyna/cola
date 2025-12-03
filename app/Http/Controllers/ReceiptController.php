<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use App\Services\TelegramService;

class ReceiptController extends Controller
{
    // Загрузка чека
    public function upload(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Необходима авторизация',
            ], 401);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:10240', // max 10MB
        ]);

        $file = $request->file('image');
        
        // Генерируем уникальное имя
        $filename = uniqid('receipt_') . '.webp';
        $path = 'receipts/' . Auth::id() . '/' . $filename;
        
        // Конвертируем в WebP и сжимаем
        $image = Image::read($file);
        
        // Уменьшаем если слишком большое (макс 1920px по большей стороне)
        $image->scaleDown(1920, 1920);
        
        // Кодируем в WebP с качеством 80%
        $encoded = $image->toWebp(80);
        
        // Сохраняем
		$fullPath = public_path('storage/' . $path);

		// Создаём папку если нет
		$directory = dirname($fullPath);
		if (!file_exists($directory)) {
			mkdir($directory, 0755, true);
		}

		file_put_contents($fullPath, $encoded);
        
        // Создаём запись в БД
        $receipt = Receipt::create([
            'user_id' => Auth::id(),
            'image_path' => $path,
            'status' => 'approved',
			'moderated_at' => now(),
        ]);
		
		$telegram = new TelegramService();
		$imageUrl = url(Storage::url($path));
		$telegram->notifyNewReceipt(Auth::user()->phone, $receipt->id, $imageUrl);
		
        return response()->json([
            'success' => true,
            'message' => 'Чек загружен',
            'receipt' => [
                'id' => $receipt->id,
                'image_url' => asset('storage/' . $path),
                'status' => $receipt->status,
                'created_at' => $receipt->created_at->format('d.m.Y H:i'),
            ],
        ]);
    }

    // Список чеков пользователя
	public function index()
	{
		if (!Auth::check()) {
			return response()->json([
				'success' => false,
				'message' => 'Необходима авторизация',
			], 401);
		}

		$drawingService = new \App\Services\DrawingService();

		$receipts = Receipt::where('user_id', Auth::id())
			->orderBy('created_at', 'desc')
			->get();

		// Группируем чеки по периодам
		$grouped = $drawingService->groupReceiptsByPeriod($receipts);

		// Форматируем для фронтенда
		$periods = [];
		foreach ($grouped as $drawingDate => $group) {
			$periodReceipts = [];
			foreach ($group['receipts'] as $receipt) {
				$receiptStatus = $drawingService->getReceiptStatus($receipt->created_at);
				$periodReceipts[] = [
					'id' => $receipt->id,
					'image_url' => asset('storage/' . $receipt->image_path),
					'status' => $receipt->status,
					'status_text' => $this->getStatusText($receipt->status),
					'reject_reason' => $receipt->reject_reason,
					'created_at' => $receipt->created_at->format('d.m.Y H:i'),
					'drawing_status' => $receiptStatus,
				];
			}

			$periods[] = [
				'drawing_name' => $group['period']['name'],
				'drawing_date' => $group['period']['drawing_date'],
				'drawing_date_formatted' => \Carbon\Carbon::parse($group['period']['drawing_date'])->format('d.m.Y'),
				'is_passed' => $group['is_passed'],
				'receipts' => $periodReceipts,
				'count' => count($periodReceipts),
			];
		}

		// Считаем шансы (только одобренные чеки)
		$chances = Receipt::where('user_id', Auth::id())
			->where('status', 'approved')
			->count();

		// Ближайший розыгрыш
		$nextDrawing = $drawingService->getNextDrawing();

		return response()->json([
			'success' => true,
			'periods' => $periods,
			'chances' => $chances,
			'total' => $receipts->count(),
			'next_drawing' => $nextDrawing,
		]);
	}

    // Удаление чека
    public function delete($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Необходима авторизация',
            ], 401);
        }

        $receipt = Receipt::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$receipt) {
            return response()->json([
                'success' => false,
                'message' => 'Чек не найден',
            ], 404);
        }

        // Удаляем файл
        $fullPath = public_path('storage/' . $receipt->image_path);
		if (file_exists($fullPath)) {
			unlink($fullPath);
		}
        
        // Удаляем запись
        $receipt->delete();

        return response()->json([
            'success' => true,
            'message' => 'Чек удалён',
        ]);
    }

    // Текст статуса
    private function getStatusText($status)
    {
        return match($status) {
            'pending' => 'На проверке',
            'approved' => 'Одобрен',
            'rejected' => 'Отклонён',
            default => $status,
        };
    }
}