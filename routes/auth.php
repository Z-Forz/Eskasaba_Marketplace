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
Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        // /admin → dashboard kalau sudah login sebagai admin, login page kalau belum
        Route::get('/', function () {
            if (auth('admin')->check()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('admin.login');
        })->name('index');

        // Halaman login admin - hanya untuk yang belum login via guard admin
        Route::middleware('guest:admin')->group(function () {
            Route::get('/login', [AdminLoginController::class, 'create'])
                ->name('login');
        });

        Route::post('/login', [AdminLoginController::class, 'store'])
            ->middleware('throttle:login')
            ->name('login.store');

    });

Route::post('/admin/logout', [AdminLoginController::class, 'destroy'])
    ->middleware('auth:admin')
    ->name('admin.logout');

// Route form ganti password
Route::middleware('auth')->group(function () {

    Route::get('/password/change', [PasswordChangeController::class, 'edit'])
        ->name('password.change');

    Route::put('/password/change', [PasswordChangeController::class, 'update'])
        ->name('password.change.update');

});

// Route user terautentikasi (wajib sudah ganti password default jika is_default_password = true)
Route::middleware(['auth', 'password.changed'])->group(function () {

    Route::get('/dashboard', DashboardRedirectController::class)
        ->name('dashboard');

    /* User profile routes */
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])
        ->name('profile.index');

    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::get('/profile/activity-logs', [\App\Http\Controllers\ProfileController::class, 'activityLogs'])
        ->name('profile.activity-logs');

    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])
        ->name('profile.update');

});