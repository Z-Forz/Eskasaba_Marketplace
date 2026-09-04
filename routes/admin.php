<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebsiteSettingController;

Route::middleware(['auth:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::post('users/sync', [UserController::class, 'sync'])
            ->name('users.sync');
        Route::resource('users', UserController::class)->except(['create', 'store']);

        Route::resource('categories', CategoryController::class);

        // Dedicated route for seller verifications
        Route::get('sellers/verifications', [SellerController::class, 'verifications'])
            ->name('sellers.verifications');

        Route::resource('sellers', SellerController::class)->except(['create', 'store']);

        // Verifikasi pengajuan seller actions
        Route::post('sellers/{seller}/approve', [SellerController::class, 'approve'])
            ->name('sellers.approve');

        Route::post('sellers/{seller}/reject', [SellerController::class, 'reject'])
            ->name('sellers.reject');

        Route::post('sellers/{seller}/revision', [SellerController::class, 'requestRevision'])
            ->name('sellers.revision');

        Route::resource('orders', OrderController::class)
            ->only(['index', 'show']);

        Route::resource('payments', PaymentController::class)
            ->only(['index', 'show', 'update']);

        // Reports routes
        Route::get('/reports/products', [ReportController::class, 'products'])
            ->name('reports.products');

        Route::get('/reports/sales', [ReportController::class, 'sales'])
            ->name('reports.sales');

        Route::get('/website-settings', [WebsiteSettingController::class, 'index'])
            ->name('website-settings.index');

        Route::put('/website-settings', [WebsiteSettingController::class, 'update'])
            ->name('website-settings.update');
    });