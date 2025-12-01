<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

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
        Storage::disk('public')->put($path, $encoded);
        
        // Создаём запись в БД
        $receipt = Receipt::create([
            'user_id' => Auth::id(),
            'image_path' => $path,
            'status' => 'approved',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Чек загружен',
            'receipt' => [
                'id' => $receipt->id,
                'image_url' => Storage::url($path),
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

        $receipts = Receipt::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($receipt) {
                return [
                    'id' => $receipt->id,
                    'image_url' => Storage::url($receipt->image_path),
                    'status' => $receipt->status,
                    'status_text' => $this->getStatusText($receipt->status),
                    'reject_reason' => $receipt->reject_reason,
                    'created_at' => $receipt->created_at->format('d.m.Y H:i'),
                ];
            });

        // Считаем шансы (только одобренные чеки)
        $chances = Receipt::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->count();

        return response()->json([
            'success' => true,
            'receipts' => $receipts,
            'chances' => $chances,
            'total' => $receipts->count(),
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
        Storage::disk('public')->delete($receipt->image_path);
        
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