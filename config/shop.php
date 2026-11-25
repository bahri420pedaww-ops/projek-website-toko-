<?php

/*
| Pengaturan toko yang dipakai di email.
|
|   SHOP_NAME="Nama Tokomu"
|   SHOP_ADMIN_EMAIL=emailkamu@gmail.com
|
| SHOP_ADMIN_EMAIL: kalau diisi, SEMUA email untuk admin (pesanan baru, pembayaran diterima)
| dikirim ke alamat ini. Kalau kosong, email dikirim ke setiap akun berperan admin di database.
*/
return [

    'name' => env('SHOP_NAME', env('APP_NAME', 'Toko Online')),

    'admin_notification_email' => env('SHOP_ADMIN_EMAIL'),
];
