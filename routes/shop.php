<?php

use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\ProductController;
use Illuminate\Support\Facades\Route;

/*
| Route toko. Dimuat dari routes/web.php lewat: require __DIR__.'/shop.php';
*/

// ---- Katalog (publik, tanpa login) ----
Route::prefix('produk')->name('shop.products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
});

// ---- Keranjang (wajib login) ----
Route::middleware('auth')->prefix('keranjang')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/', [CartController::class, 'store'])->name('store');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
    Route::patch('/{item}', [CartController::class, 'update'])->whereNumber('item')->name('update');
    Route::delete('/{item}', [CartController::class, 'destroy'])->whereNumber('item')->name('destroy');
});
