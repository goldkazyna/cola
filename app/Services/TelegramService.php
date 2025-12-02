<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $token;
    private string $chatId;
    private string $apiUrl = 'https://api.telegram.org/bot';

    public function __construct()
    {
        $this->token = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    // Отправка сообщения
    public function sendMessage(string $message): bool
    {
        if (empty($this->token) || empty($this->chatId)) {
            Log::warning('Telegram credentials not configured');
            return false;
        }

        try {
            $response = Http::withoutVerifying()->post($this->apiUrl . $this->token . '/sendMessage', [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if (!$response->successful()) {
                Log::error('Telegram API error', [
                    'response' => $response->json(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Telegram send error: ' . $e->getMessage());
            return false;
        }
    }

    // Уведомление о новом пользователе
    public function notifyNewUser(string $phone): bool
    {
        $message = "👤 <b>Новый пользователь</b>\n\n";
        $message .= "📱 Телефон: <code>{$phone}</code>\n";
        $message .= "📅 Дата: " . now()->format('d.m.Y H:i');

        return $this->sendMessage($message);
    }

    // Уведомление о новом чеке
    public function notifyNewReceipt(string $phone, int $receiptId, string $imageUrl): bool
    {
        $message = "🧾 <b>Новый чек загружен</b>\n\n";
        $message .= "📱 Телефон: <code>{$phone}</code>\n";
        $message .= "🆔 ID чека: {$receiptId}\n";
        $message .= "📅 Дата: " . now()->format('d.m.Y H:i') . "\n";
        $message .= "🔗 <a href=\"{$imageUrl}\">Посмотреть чек</a>";

        return $this->sendMessage($message);
    }

    // Статистика
    public function getStats(): string
    {
        $usersCount = \App\Models\User::count();
        $receiptsCount = \App\Models\Receipt::count();
        $approvedCount = \App\Models\Receipt::where('status', 'approved')->count();
        $rejectedCount = \App\Models\Receipt::where('status', 'rejected')->count();

        $message = "📊 <b>Статистика акции</b>\n\n";
        $message .= "👥 Пользователей: {$usersCount}\n";
        $message .= "🧾 Всего чеков: {$receiptsCount}\n";
        $message .= "✅ Одобрено: {$approvedCount}\n";
        $message .= "❌ Отклонено: {$rejectedCount}";

        return $message;
    }
}