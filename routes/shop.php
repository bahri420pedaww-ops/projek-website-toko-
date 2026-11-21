<?php

use App\Http\Controllers\Shop\ProductController;
use Illuminate\Support\Facades\Route;

/*
| Route publik toko (tanpa login).
| Dimuat dari routes/web.php lewat: require __DIR__.'/shop.php';
*/
Route::prefix('produk')->name('shop.products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
});
