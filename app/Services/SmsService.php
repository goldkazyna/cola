<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private string $apiUrl = 'http://212.124.121.186:9507/api';
    private string $username = 'tvinhrkz1';
    private string $password = 'MofEI1c5I';
    private string $originator = 'KiT_Notify';

    public function sendCode(string $phone, string $code): bool
    {
        // Убираем + из номера
        $phone = ltrim($phone, '+');
        
        // Формируем сообщение
        $message = "SMALL: Ваш код авторизации: {$code}";

        try {
            $response = Http::get($this->apiUrl, [
                'action' => 'sendmessage',
                'username' => $this->username,
                'password' => $this->password,
                'recipient' => $phone,
                'messagetype' => 'SMS:TEXT',
                'originator' => $this->originator,
                'messagedata' => $message,
            ]);

            Log::info("SMS отправлена на {$phone}", [
                'code' => $code,
                'response' => $response->body(),
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Ошибка отправки SMS: " . $e->getMessage());
            return false;
        }
    }
}