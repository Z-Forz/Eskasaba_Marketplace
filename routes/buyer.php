<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Buyer\DashboardController;
use App\Http\Controllers\Buyer\CartController;
use App\Http\Controllers\Buyer\CheckoutController;
use App\Http\Controllers\Buyer\OrderController;
use App\Http\Controllers\Buyer\ReviewController;
use App\Http\Controllers\SellerApplicationController;
use App\Http\Controllers\NotificationController;

Route::middleware(['auth', 'password.changed'])
    ->prefix('buyer')
    ->name('buyer.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('cart', CartController::class);

        Route::get('/checkout', [CheckoutController::class, 'index'])
            ->name('checkout.index');

        Route::post('/checkout', [CheckoutController::class, 'store'])
            ->name('checkout.store');

        Route::resource('orders', OrderController::class)
            ->only(['index', 'show']);

        Route::get('/reviews/create/{order}', [ReviewController::class, 'create'])
            ->name('reviews.create');

        Route::resource('reviews', ReviewController::class)
            ->only(['store', 'update', 'destroy']);

        // Pengajuan menjadi seller
        Route::get('/apply-seller', [SellerApplicationController::class, 'create'])
            ->name('apply-seller');

        Route::post('/apply-seller', [SellerApplicationController::class, 'store'])
            ->name('apply-seller.store');

        // Notifikasi sistem in-app
        Route::get('/notifications', [NotificationController::class, 'index'])
            ->name('notifications.index');

        Route::get('/notifications/{notification}/read', [NotificationController::class, 'read'])
            ->name('notifications.read');

        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])
            ->name('notifications.read-all');
    });