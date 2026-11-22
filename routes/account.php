<?php

use App\Http\Controllers\Account\AddressController;
use Illuminate\Support\Facades\Route;

/*
| Area akun pembeli (wajib login).
| Dimuat dari routes/web.php lewat: require __DIR__.'/account.php';
*/
Route::middleware('auth')->prefix('akun/alamat')->name('addresses.')->group(function () {
    Route::get('/', [AddressController::class, 'index'])->name('index');
    Route::get('/tambah', [AddressController::class, 'create'])->name('create');
    Route::post('/', [AddressController::class, 'store'])->name('store');
    Route::get('/{address}/ubah', [AddressController::class, 'edit'])->whereNumber('address')->name('edit');
    Route::put('/{address}', [AddressController::class, 'update'])->whereNumber('address')->name('update');
    Route::patch('/{address}/utama', [AddressController::class, 'setDefault'])->whereNumber('address')->name('default');
    Route::delete('/{address}', [AddressController::class, 'destroy'])->whereNumber('address')->name('destroy');
});
