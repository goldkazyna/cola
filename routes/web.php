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