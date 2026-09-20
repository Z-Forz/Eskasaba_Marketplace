<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\SellerRequestController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\Admin\WhatsAppController;

Route::middleware(['auth:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::post('users/sync', [UserController::class, 'sync'])
            ->name('users.sync');
        Route::resource('users', UserController::class);

        Route::resource('categories', CategoryController::class);

        // Dedicated route for seller verifications
        Route::get('sellers/verifications', [SellerController::class, 'verifications'])
            ->name('sellers.verifications');

        Route::resource('sellers', SellerController::class);

        // Verifikasi pengajuan seller actions
        Route::post('sellers/{seller}/approve', [SellerController::class, 'approve'])
            ->name('sellers.approve');

        Route::post('sellers/{seller}/reject', [SellerController::class, 'reject'])
            ->name('sellers.reject');

        Route::post('sellers/{seller}/revision', [SellerController::class, 'requestRevision'])
            ->name('sellers.revision');

        // Seller Requests Management
        Route::get('seller-requests', [SellerRequestController::class, 'index'])
            ->name('seller-requests.index');
        Route::get('seller-requests/{sellerRequest}', [SellerRequestController::class, 'show'])
            ->name('seller-requests.show');
        Route::post('seller-requests/{sellerRequest}/confirm', [SellerRequestController::class, 'confirm'])
            ->name('seller-requests.confirm');
        Route::post('seller-requests/{sellerRequest}/reject', [SellerRequestController::class, 'reject'])
            ->name('seller-requests.reject');

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

        // WhatsApp Bot Control Routes
        Route::get('/whatsapp', [WhatsAppController::class, 'index'])
            ->name('whatsapp.index');
        Route::get('/whatsapp/status', [WhatsAppController::class, 'status'])
            ->name('whatsapp.status');
        Route::post('/whatsapp/start', [WhatsAppController::class, 'start'])
            ->name('whatsapp.start');
        Route::post('/whatsapp/stop', [WhatsAppController::class, 'stop'])
            ->name('whatsapp.stop');
        Route::post('/whatsapp/disconnect', [WhatsAppController::class, 'disconnect'])
            ->name('whatsapp.disconnect');
        Route::post('/whatsapp/reset-session', [WhatsAppController::class, 'resetSession'])
            ->name('whatsapp.reset-session');
    });