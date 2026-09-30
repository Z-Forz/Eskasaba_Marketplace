<?php

use App\Http\Controllers\Api\PaymentCallbackController;
use App\Http\Controllers\Auth\SchoolCallbackController;
use App\Http\Controllers\OAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API & Callback Routes
|--------------------------------------------------------------------------
*/

// Payment Gateway / Webhook Callback (Midtrans, Tripay, QRIS, Bank Transfer)
Route::match(['get', 'post'], '/payment/callback', [PaymentCallbackController::class, 'handle'])
    ->name('api.payment.callback');

Route::match(['get', 'post'], '/payments/callback', [PaymentCallbackController::class, 'handle'])
    ->name('api.payments.callback');

// School API / SSO Callback
Route::match(['get', 'post'], '/school/callback', [SchoolCallbackController::class, 'handle'])
    ->name('api.school.callback');

// SiPintu Webhook: Sinkronisasi Otomatis Data Pengguna & Password Real-time
Route::match(['get', 'post'], '/sipintu/sync-user', [OAuthController::class, 'syncUser'])
    ->name('api.sipintu.sync-user');

Route::match(['get', 'post'], '/sipintu/sync-password', [OAuthController::class, 'syncPassword'])
    ->name('api.sipintu.sync-password');
