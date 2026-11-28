<?php

use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\OrderActionController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\Shop\ProductController;
use Illuminate\Support\Facades\Route;

/*
| Route toko. Dimuat dari routes/web.php lewat: require __DIR__.'/shop.php';
| "throttle:X,1" = maksimal X permintaan per menit per pengguna/alamat IP (mencegah penyalahgunaan).
*/

// ---- Katalog (publik, tanpa login) ----
Route::prefix('produk')->name('shop.products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
});

// ---- Keranjang (wajib login) ----
Route::middleware('auth')->prefix('keranjang')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/', [CartController::class, 'store'])->middleware('throttle:60,1')->name('store');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
    Route::patch('/{item}', [CartController::class, 'update'])->whereNumber('item')->name('update');
    Route::delete('/{item}', [CartController::class, 'destroy'])->whereNumber('item')->name('destroy');
});

// ---- Checkout dan pesanan saya (wajib login) ----
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');

    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{orderCode}', [OrderController::class, 'show'])->name('orders.show');

    // Aksi pembeli pada pesanannya sendiri
    Route::post('/pesanan/{orderCode}/batal', [OrderActionController::class, 'cancel'])
        ->middleware('throttle:20,1')->name('orders.cancel');
    Route::post('/pesanan/{orderCode}/diterima', [OrderActionController::class, 'receive'])
        ->middleware('throttle:20,1')->name('orders.receive');
});
