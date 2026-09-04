<?php

use App\Http\Controllers\Auth\SchoolCallbackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API & Callback Routes
|--------------------------------------------------------------------------
*/

// School API / SSO Callback
Route::match(['get', 'post'], '/school/callback', [SchoolCallbackController::class, 'handle'])
    ->name('api.school.callback');
