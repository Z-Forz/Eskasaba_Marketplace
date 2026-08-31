<?php

use App\Http\Controllers\Api\PaymentCallbackController;
use App\Http\Controllers\Auth\SchoolCallbackController;
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
