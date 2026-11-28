<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ShippingMethodController;
use Illuminate\Support\Facades\Route;

/*
| Semua route di sini hanya bisa diakses oleh user yang sudah login
| DAN berstatus admin (middleware 'auth' dan 'admin').
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('products', ProductController::class)->except('show');

    Route::delete('products/{product}/images/{image}', [ProductImageController::class, 'destroy'])
        ->name('products.images.destroy');
    Route::patch('products/{product}/images/{image}/primary', [ProductImageController::class, 'setPrimary'])
        ->name('products.images.primary');

    // Pelanggan dan alamatnya (hanya lihat)
    Route::resource('customers', CustomerController::class)->only(['index', 'show']);

    // Layanan kurir dan tarif ongkir
    Route::resource('shipping-methods', ShippingMethodController::class)
        ->parameters(['shipping-methods' => 'method'])
        ->except(['show', 'destroy']);

    // Pesanan: lihat dan ubah status
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::post('orders/{order}/process', [OrderController::class, 'process'])->name('orders.process');
    Route::post('orders/{order}/ship', [OrderController::class, 'ship'])->name('orders.ship');
    Route::post('orders/{order}/deliver', [OrderController::class, 'deliver'])->name('orders.deliver');
    Route::post('orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Laporan penjualan
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
});
