<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/products', [HomeController::class, 'products'])
    ->name('products.index');

Route::get('/products/{product}', [HomeController::class, 'show'])
    ->name('products.show');