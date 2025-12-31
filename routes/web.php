<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReceiptController as AdminReceiptController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return view('welcome');
});

// API для авторизации
Route::post('/auth/send-code', [AuthController::class, 'sendCode']);
Route::post('/auth/verify-code', [AuthController::class, 'verifyCode']);
Route::get('/auth/check', [AuthController::class, 'check']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/verify-phone', [AuthController::class, 'verifyPhone']);

// API для чеков
Route::post('/receipts/upload', [ReceiptController::class, 'upload']);
Route::get('/receipts', [ReceiptController::class, 'index']);
Route::delete('/receipts/{id}', [ReceiptController::class, 'delete']);
Route::post('/auth/verify-by-phone', [AuthController::class, 'verifyByPhone']);
// Админ панель
Route::prefix('admin')->name('admin.')->group(function () {
    // Авторизация
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Защищённые роуты
    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Чеки
        Route::get('/receipts', [AdminReceiptController::class, 'index'])->name('receipts');
        Route::post('/receipts/{id}/reject', [AdminReceiptController::class, 'reject'])->name('receipts.reject');
        Route::post('/receipts/{id}/approve', [AdminReceiptController::class, 'approve'])->name('receipts.approve');
        Route::delete('/receipts/{id}', [AdminReceiptController::class, 'delete'])->name('receipts.delete');
        Route::get('/receipts/export', [AdminReceiptController::class, 'export'])->name('receipts.export');
        
        // Пользователи
        Route::get('/users', [UserController::class, 'index'])->name('users');
    });
});

Route::post('/log/error', function (\Illuminate\Http\Request $request) {
    $logData = [
        'time' => now()->toDateTimeString(),
        'type' => $request->input('type'),
        'message' => $request->input('message'),
        'status' => $request->input('status'),
        'url' => $request->input('url'),
        'user_id' => \Illuminate\Support\Facades\Auth::id(),
        'phone' => \Illuminate\Support\Facades\Auth::user()?->phone,
        'user_agent' => $request->userAgent(),
        'ip' => $request->ip(),
    ];
    
    $line = '[' . $logData['time'] . '] CLIENT ERROR: ' . json_encode($logData, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    
    file_put_contents(storage_path('logs/client-errors.log'), $line, FILE_APPEND);
    
    return response()->json(['logged' => true]);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);