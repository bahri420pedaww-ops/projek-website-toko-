<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ShippingMethodController;
use Illuminate\Support\Facades\Route;

/*
| Semua route di sini hanya bisa diakses oleh user yang sudah login
| DAN berstatus admin (middleware 'auth' dan 'admin').
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');

    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('products', ProductController::class)->except('show');

    Route::delete('products/{product}/images/{image}', [ProductImageController::class, 'destroy'])
        ->name('products.images.destroy');
    Route::patch('products/{product}/images/{image}/primary', [ProductImageController::class, 'setPrimary'])
        ->name('products.images.primary');

    // Pelanggan dan alamatnya (hanya lihat)
    Route::resource('customers', CustomerController::class)->only(['index', 'show']);

    // Layanan kurir dan tarif ongkir per zona
    Route::resource('shipping-methods', ShippingMethodController::class)
        ->parameters(['shipping-methods' => 'method'])
        ->except(['show', 'destroy']);
});
