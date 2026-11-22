<?php

/*
| Pengaturan ongkos kirim.
|
| Ongkir = tarif per kg (sesuai zona tujuan) x berat yang ditagih (dibulatkan ke atas, minimal 1 kg).
|
| Zona ditentukan dari lokasi TOKO (asal) ke alamat tujuan:
|   1 = satu kota dengan toko
|   2 = satu provinsi dengan toko
|   3 = satu pulau besar dengan toko
|   4 = beda pulau
|
| Atur lokasi toko di file .env:
|   SHOP_ORIGIN_CITY="Padang"
|   SHOP_ORIGIN_PROVINCE="Sumatera Barat"
*/
return [

    'origin_city'     => env('SHOP_ORIGIN_CITY', 'Padang'),
    'origin_province' => env('SHOP_ORIGIN_PROVINCE', 'Sumatera Barat'),

    // Pengelompokan provinsi per pulau. Semua nama ditulis huruf kecil, boleh ada alias.
    'islands' => [
        'sumatera' => [
            'aceh', 'nanggroe aceh darussalam', 'sumatera utara', 'sumatera barat', 'riau',
            'kepulauan riau', 'jambi', 'sumatera selatan', 'kepulauan bangka belitung',
            'bangka belitung', 'bengkulu', 'lampung',
        ],
        'jawa' => [
            'banten', 'dki jakarta', 'jakarta', 'daerah khusus ibukota jakarta',
            'daerah khusus ibu kota jakarta', 'jawa barat', 'jawa tengah', 'di yogyakarta',
            'yogyakarta', 'daerah istimewa yogyakarta', 'jawa timur',
        ],
        'bali_nusa_tenggara' => [
            'bali', 'nusa tenggara barat', 'nusa tenggara timur', 'ntb', 'ntt',
        ],
        'kalimantan' => [
            'kalimantan barat', 'kalimantan tengah', 'kalimantan selatan',
            'kalimantan timur', 'kalimantan utara',
        ],
        'sulawesi' => [
            'sulawesi utara', 'gorontalo', 'sulawesi tengah', 'sulawesi barat',
            'sulawesi selatan', 'sulawesi tenggara',
        ],
        'maluku_papua' => [
            'maluku', 'maluku utara', 'papua', 'papua barat', 'papua barat daya',
            'papua selatan', 'papua tengah', 'papua pegunungan',
        ],
    ],
];
