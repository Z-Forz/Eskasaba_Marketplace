<?php

use App\Http\Controllers\Auth\SchoolCallbackController;
use App\Http\Controllers\Api\SiPintuWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API & Callback Routes
|--------------------------------------------------------------------------
*/

// Health Check Endpoint (SiPintu Downstream Monitoring)
Route::get('/health', fn () => response()->json(['status' => 'ok', 'service' => config('app.name'), 'time' => now()->toIso8601String()]))
    ->name('api.health');

// School API / SSO Callback
Route::match(['get', 'post'], '/school/callback', [SchoolCallbackController::class, 'handle'])
    ->name('api.school.callback');

// Webhook Sinkronisasi Password dari SiPintu Gateway
Route::post('/sipintu/sync-password', [SiPintuWebhookController::class, 'syncPassword'])
    ->name('api.sipintu.sync-password');
