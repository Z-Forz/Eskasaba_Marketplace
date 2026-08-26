<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\PaymentController;
use App\Http\Controllers\Seller\PickupScheduleController;
use App\Http\Controllers\Seller\ProfileController;

Route::middleware(['auth', 'password.changed', 'seller.approved'])
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

        Route::resource('payments', PaymentController::class)
            ->only(['index', 'show']);

        Route::resource('pickup-schedules', PickupScheduleController::class)
            ->only(['index', 'show', 'update']);
    });