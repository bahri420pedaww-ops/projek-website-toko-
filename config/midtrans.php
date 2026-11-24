<?php

/*
| Pengaturan Midtrans (Snap). Isi kunci di file .env:
|
|   MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxx
|   MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxx
|   MIDTRANS_IS_PRODUCTION=false
|   MIDTRANS_EXPIRY_HOURS=24
|   MIDTRANS_VERIFY_SSL=true
|
| JANGAN PERNAH commit file .env. Server Key bersifat rahasia.
*/
$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);

return [

    'server_key'    => env('MIDTRANS_SERVER_KEY'),
    'client_key'    => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => $isProduction,

    'snap_url'    => $isProduction
        ? 'https://app.midtrans.com/snap/v1/transactions'
        : 'https://app.sandbox.midtrans.com/snap/v1/transactions',
    'snap_js_url' => $isProduction
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',
    'api_url'     => $isProduction
        ? 'https://api.midtrans.com'
        : 'https://api.sandbox.midtrans.com',

    // Batas waktu pembayaran (jam). Dipakai untuk masa berlaku token Snap
    // dan untuk membatalkan otomatis pesanan yang tidak dibayar.
    'expiry_hours' => (int) env('MIDTRANS_EXPIRY_HOURS', 24),

    // Set false HANYA untuk debugging lokal jika muncul "cURL error 60: SSL certificate problem".
    'verify_ssl' => (bool) env('MIDTRANS_VERIFY_SSL', true),
];
