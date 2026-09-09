<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/products', [HomeController::class, 'products'])
    ->name('products.index');

Route::get('/products/{product}', [HomeController::class, 'show'])
    ->name('products.show');

Route::view('/panduan', 'guide')->name('guide');

Route::view('/tentang', 'about')->name('about');
 
// Health Check Endpoint (SiPintu Downstream Monitoring)
Route::get('/health', fn () => response()->json(['status' => 'ok', 'service' => config('app.name'), 'time' => now()->toIso8601String()]))
    ->name('health');

// WhatsApp Gateway Proxy Route (Proxies https://eskamart.smkn1bangsri.sch.id/send-message to local Baileys bot)
Route::match(['get', 'post'], '/send-message', function (\Illuminate\Http\Request $request) {
    if ($request->isMethod('get')) {
        return response()->json([
            'status'  => true,
            'message' => 'WhatsApp Gateway Proxy Endpoint Active',
            'gateway' => 'Eskasaba Baileys Bot',
        ]);
    }

    try {
        $response = \Illuminate\Support\Facades\Http::withoutVerifying()
            ->timeout(10)
            ->post('http://localhost:3000/send-message', $request->all());

        return response()->json($response->json(), $response->status());
    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Gagal terhubung ke WhatsApp Baileys Bot lokal: ' . $e->getMessage(),
        ], 503);
    }
})->name('wa.send-message');