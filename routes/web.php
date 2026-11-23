<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
});
Route::view('/preview/home', 'home');
Route::view('/preview/katalog', 'products.index');
Route::view('/preview/produk', 'products.show');
Route::view('/preview/keranjang', 'cart');
Route::get('/preview/detail', function () {
    return view('show');
});