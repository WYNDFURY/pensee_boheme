<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
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

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('carts')->name('carts.')->group(function () {
    Route::post('/', [CartController::class, 'store'])->name('store');
    Route::get('/{cart}', [CartController::class, 'show'])->name('show');
    Route::delete('/{database}', [CartController::class, 'destroy'])->name('destroy');
});

Route::prefix('cart-items')->name('cart-items.')->group(function () {
    Route::post('/', [CartItemController::class, 'store'])->name('store');
    Route::delete('/{cartItem}', [CartItemController::class, 'destroy'])->name('destroy');
});

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
});

Route::prefix('register')->name('register.')->group(function () {
    Route::post('/', [RegisteredUserController::class, 'store'])->name('store');
});
