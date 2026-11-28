<?php

use App\Http\Controllers\Shop\MidtransNotificationController;
use App\Http\Controllers\Shop\PaymentController;
use Illuminate\Support\Facades\Route;

/*
| Route pembayaran. Dimuat dari routes/web.php lewat: require __DIR__.'/payment.php';
| "throttle:X,1" = maksimal X permintaan per menit (mencegah penyalahgunaan).
*/

// ---- Pembeli (wajib login) ----
Route::middleware('auth')->group(function () {
    Route::get('/pesanan/{orderCode}/bayar', [PaymentController::class, 'show'])
        ->middleware('throttle:30,1')->name('payments.show');
    Route::post('/pesanan/{orderCode}/cek-pembayaran', [PaymentController::class, 'check'])
        ->middleware('throttle:20,1')->name('payments.check');
    // Dipanggil otomatis tiap 5 detik oleh halaman pesanan (12 kali per menit); batas diberi ruang lebih
    Route::get('/pesanan/{orderCode}/status', [PaymentController::class, 'status'])
        ->middleware('throttle:60,1')->name('payments.status');
});

// ---- Webhook Midtrans ----
// Publik (dipanggil oleh server Midtrans) dan tanpa CSRF. Keasliannya dicek lewat signature.
Route::post('/midtrans/notification', MidtransNotificationController::class)
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->middleware('throttle:300,1')
    ->name('payments.notification');
