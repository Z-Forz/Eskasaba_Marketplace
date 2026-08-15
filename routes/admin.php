<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebsiteSettingController;

Route::middleware(['auth:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('users', UserController::class);

        Route::resource('categories', CategoryController::class);

        Route::resource('sellers', SellerController::class);

        Route::resource('orders', OrderController::class)
            ->only(['index', 'show']);

        Route::resource('payments', PaymentController::class)
            ->only(['index', 'show', 'update']);

        Route::get('/website-settings', [WebsiteSettingController::class, 'index'])
            ->name('website-settings.index');

        Route::put('/website-settings', [WebsiteSettingController::class, 'update'])
            ->name('website-settings.update');
    });