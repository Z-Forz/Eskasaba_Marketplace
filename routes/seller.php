<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\PaymentController;
use App\Http\Controllers\Seller\PickupScheduleController;
use App\Http\Controllers\Seller\ProfileController;
use App\Http\Controllers\Seller\SellerRequestController;

Route::middleware(['auth', 'seller.approved'])
    ->prefix('seller')
    ->name('seller.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::put('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::resource('products', ProductController::class);

        Route::resource('orders', OrderController::class)
            ->only(['index', 'show', 'update']);

        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])
            ->name('orders.cancel');

        Route::post('/orders/{order}/confirm-cancellation', [OrderController::class, 'confirmCancellation'])
            ->name('orders.confirm-cancellation');

        Route::resource('payments', PaymentController::class)
            ->only(['index', 'show']);

        Route::resource('pickup-schedules', PickupScheduleController::class)
            ->only(['index', 'show', 'update']);

        Route::resource('seller-requests', SellerRequestController::class)
            ->only(['index', 'create', 'store', 'show']);
    });