<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\DashboardRedirectController;
use App\Http\Controllers\Auth\SchoolCallbackController;
use App\Http\Controllers\Auth\SchoolLoginController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Login siswa/guru (NIS/NIP, lewat API Sekolah)
Route::middleware('guest:web')->group(function () {

    Route::get('/login', [SchoolLoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [SchoolLoginController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.store');

    // Callback SSO API Sekolah (Legacy)
    Route::match(['get', 'post'], '/auth/school/callback', [SchoolCallbackController::class, 'handle'])
        ->name('auth.school.callback');

});

// Endpoint penerima redirect SSO otomatis dari SiPintu Gateway (Terbuka untuk login/re-login seamless)
Route::match(['get', 'post'], '/oauth/callback', [OAuthController::class, 'callback'])
    ->name('oauth.callback');

Route::match(['get', 'post'], '/logout', [SchoolLoginController::class, 'logout'])
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

            Route::post('/login', [AdminLoginController::class, 'store'])
                ->middleware('throttle:login')
                ->name('login.store');
        });

    });

Route::post('/admin/logout', [AdminLoginController::class, 'destroy'])
    ->middleware('auth:admin')
    ->name('admin.logout');

// Route user terautentikasi
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', DashboardRedirectController::class)
        ->name('dashboard');

    /* User profile routes */
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::get('/profile/activity-logs', [ProfileController::class, 'activityLogs'])
        ->name('profile.activity-logs');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

});
