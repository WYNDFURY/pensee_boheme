<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CartProductController;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\HasAccessToCart;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
});

Route::prefix('me')->name('me.')->group(function () {
    Route::get('user', [MeController::class, 'user'])->name('user');
    Route::get('cart', [MeController::class, 'cart'])->name('cart');
});

Route::prefix('carts')->name('carts.')->group(function () {
    Route::prefix('{cart}')->middleware(HasAccessToCart::class)->group(function () {
        Route::patch('empty', [CartController::class, 'empty'])->name('empty');
        Route::patch('transfer', [CartController::class, 'transfer'])->middleware('auth:sanctum')->name('transfer');
        Route::prefix('products/{product}')->name('products.')->group(function () {
            Route::put('/', [CartProductController::class, 'update'])->name('update');
            Route::delete('/', [CartProductController::class, 'destroy'])->name('destroy');
        });
    });
});

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{product}', [ProductController::class, 'show']);
});
