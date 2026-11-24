<?php

use App\Http\Controllers\Shop\MidtransNotificationController;
use App\Http\Controllers\Shop\PaymentController;
use Illuminate\Support\Facades\Route;

/*
| Route pembayaran. Dimuat dari routes/web.php lewat: require __DIR__.'/payment.php';
*/

// ---- Pembeli (wajib login) ----
Route::middleware('auth')->group(function () {
    Route::get('/pesanan/{orderCode}/bayar', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/pesanan/{orderCode}/cek-pembayaran', [PaymentController::class, 'check'])->name('payments.check');
});

// ---- Webhook Midtrans ----
// Publik (dipanggil oleh server Midtrans) dan tanpa CSRF. Keasliannya dicek lewat signature.
Route::post('/midtrans/notification', MidtransNotificationController::class)
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('payments.notification');
