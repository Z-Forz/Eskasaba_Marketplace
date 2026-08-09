<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\SchoolLoginController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Auth\DashboardRedirectController;

// Login siswa/guru (NIS/NIP, lewat API Sekolah)
Route::middleware('guest')->group(function () {

    Route::get('/login', [SchoolLoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [SchoolLoginController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.store');

});

Route::post('/logout', [SchoolLoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Login admin (username lokal, terpisah dari API Sekolah)
Route::middleware('guest')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/login', [AdminLoginController::class, 'create'])
            ->name('login');

        Route::post('/login', [AdminLoginController::class, 'store'])
            ->middleware('throttle:login')
            ->name('login.store');

    });

Route::post('/admin/logout', [AdminLoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('admin.logout');

// Wajib ganti password default + redirect dashboard sesuai role
Route::middleware('auth')->group(function () {

    Route::get('/password/change', [PasswordChangeController::class, 'edit'])
        ->name('password.change');

    Route::put('/password/change', [PasswordChangeController::class, 'update'])
        ->name('password.update');

    Route::get('/dashboard', DashboardRedirectController::class)
        ->name('dashboard');

});