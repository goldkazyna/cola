<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SmsCode;
use App\Services\SmsService;
use App\Services\TelegramService;
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
            'name' => 'required|string|min:2|max:100',
            'surname' => 'required|string|min:2|max:100',
            'city' => 'required|string|min:2|max:100',
        ]);

        $phone = preg_replace('/[^0-9+]/', '', $request->phone);

        // Удаляем старые коды для этого номера
        SmsCode::where('phone', $phone)->delete();

        // Генерируем 4-значный код
        $code = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        // Сохраняем код (действует 5 минут)
        SmsCode::create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // Отправляем SMS
        $smsService = new SmsService();
        $sent = $smsService->sendCode($phone, $code);

        if (!$sent) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка отправки SMS. Попробуйте позже.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Код отправлен',
        ]);
    }

    // Проверка кода и авторизация
    public function verifyCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string|size:4',
            'name' => 'required|string|min:2|max:100',
            'surname' => 'required|string|min:2|max:100',
            'city' => 'required|string|min:2|max:100',
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

        // Создаём или находим юзера
        $user = User::firstOrCreate(
            ['phone' => $phone],
            ['name' => $request->name, 'surname' => $request->surname, 'city' => $request->city]
        );

        // Если юзер уже был — обновляем данные
        if (!$user->wasRecentlyCreated) {
            $user->update([
                'name' => $request->name,
                'surname' => $request->surname,
                'city' => $request->city,
            ]);
        }

        if ($user->wasRecentlyCreated) {
            $telegram = new TelegramService();
            $telegram->notifyNewUser($phone);
        }

        Auth::login($user, true);

        return response()->json([
            'success' => true,
            'message' => 'Успешная авторизация',
            'user' => [
                'id' => $user->id,
                'phone' => $user->phone,
                'name' => $user->name,
                'surname' => $user->surname,
                'city' => $user->city,
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
                    'name' => Auth::user()->name,
                    'surname' => Auth::user()->surname,
                    'city' => Auth::user()->city,
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