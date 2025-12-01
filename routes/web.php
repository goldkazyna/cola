<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// API для авторизации
Route::post('/auth/send-code', [AuthController::class, 'sendCode']);
Route::post('/auth/verify-code', [AuthController::class, 'verifyCode']);
Route::get('/auth/check', [AuthController::class, 'check']);
Route::post('/auth/logout', [AuthController::class, 'logout']);