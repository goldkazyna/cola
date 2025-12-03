<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SmsCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    // Отправка SMS-кода
    public function sendCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:20',
        ]);

        // Очищаем номер от лишних символов
        $phone = preg_replace('/[^0-9+]/', '', $request->phone);

        // Удаляем старые коды для этого номера
        SmsCode::where('phone', $phone)->delete();

        // Генерируем 4-значный код
        $code = '1111';

        // Сохраняем код (действует 5 минут)
        SmsCode::create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // TODO: Здесь будет отправка SMS через шлюз
        // Пока просто логируем код для тестирования
        \Log::info("SMS Code for {$phone}: {$code}");

        return response()->json([
            'success' => true,
            'message' => 'Код отправлен',
            // Для тестирования показываем код (убрать в продакшене!)
            'debug_code' => $code,
        ]);
    }

    // Проверка кода и авторизация
    public function verifyCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string|size:4',
        ]);

        $phone = preg_replace('/[^0-9+]/', '', $request->phone);

        // Ищем действующий код
        $smsCode = SmsCode::where('phone', $phone)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$smsCode) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный код или код истёк',
            ], 422);
        }

        // Код верный — удаляем его
        $smsCode->delete();

        // Находим или создаём пользователя
        $user = User::firstOrCreate(['phone' => $phone]);
		
		if ($user->wasRecentlyCreated) {
			$telegram = new \App\Services\TelegramService();
			$telegram->notifyNewUser($phone);
		}	
        // Авторизуем с запоминанием на 30 дней
        Auth::login($user, true);

        return response()->json([
            'success' => true,
            'message' => 'Успешная авторизация',
            'user' => [
                'id' => $user->id,
                'phone' => $user->phone,
            ],
			'csrf_token' => csrf_token(),
        ]);
    }

    // Проверка статуса авторизации
    public function check()
    {
        if (Auth::check()) {
            return response()->json([
                'authenticated' => true,
                'user' => [
                    'id' => Auth::user()->id,
                    'phone' => Auth::user()->phone,
                ],
            ]);
        }

        return response()->json([
            'authenticated' => false,
        ]);
    }

    // Выход
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Вы вышли из системы',
        ]);
    }
}