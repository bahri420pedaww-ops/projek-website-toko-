# Kontrak Backend - Frontend: Checkout dan Pesanan (versi ongkir dinamis)

Menggantikan versi sebelumnya. Nama route, nama field form, dan nama variabel jangan
diubah sepihak. Semua file di `resources/views/shop/` adalah PLACEHOLDER dan boleh ditimpa desain.

## Aturan umum

- Semua halaman ini **wajib login**. Semua aksi memakai form POST dengan `@csrf`.
- Pesan dari backend lewat session `success`, `warning`, `error` (`@include('shop._flash')`).
- Harga, stok, **ongkir**, dan total **dihitung ulang oleh backend** saat pesanan dibuat.
  Angka di halaman checkout hanya tampilan.
- Alamat, harga, berat, dan ongkir **disalin** ke pesanan saat checkout. Mengubah alamat atau
  harga produk setelahnya tidak mengubah pesanan lama.
- Stok berkurang saat pesanan dibuat (bukan saat dibayar).

## Cara ongkir dihitung

```
ongkir = tarif per kg (sesuai zona tujuan) x berat yang ditagih
berat yang ditagih = berat total (gram) / 1000, dibulatkan ke atas, minimal 1 kg
```

Zona ditentukan dari lokasi toko ke kota dan provinsi alamat tujuan:

| Zona | Arti |
|---|---|
| 1 | Satu kota dengan toko |
| 2 | Satu provinsi dengan toko |
| 3 | Satu pulau besar dengan toko |
| 4 | Beda pulau |

Satu layanan kurir bisa **tidak melayani** zona tertentu. Untuk kombinasi seperti itu,
`$quotes[alamat][layanan]` bernilai `null`; tampilkan "tidak melayani tujuan ini" dan
nonaktifkan pilihannya. Estimasi hari (`etd`) bisa kosong.

## Alur

1. Keranjang -> tombol "Lanjut ke Checkout" -> `route('checkout.index')`
2. Checkout -> form POST -> `route('checkout.store')`
3. Berhasil -> diarahkan ke `route('orders.show', $order->order_code)`

Pengalihan otomatis dari `/checkout`: keranjang kosong atau bermasalah kembali ke keranjang,
belum punya alamat diarahkan ke form tambah alamat.

## Route

| Halaman | Method | URL | Nama route |
|---|---|---|---|
| Checkout | GET | `/checkout` | `checkout.index` |
| Buat pesanan | POST | `/checkout` | `checkout.store` |
| Daftar pesanan saya | GET | `/pesanan` | `orders.index` |
| Detail pesanan | GET | `/pesanan/{order_code}` | `orders.show` |

## Form checkout (POST `checkout.store`)

| Field | Aturan |
|---|---|
| `address_id` | Wajib. ID alamat milik pembeli |
| `shipping_method_id` | Wajib. ID layanan aktif yang melayani tujuan alamat itu |
| `note` | Opsional, maksimal 255 karakter |

Kesalahan validasi tersedia lewat `@error('address_id')` dan sejenisnya. Kesalahan proses
(stok, layanan tidak melayani tujuan) dikirim lewat session `error`.

## Variabel di `shop/checkout/index`

| Variabel | Isi |
|---|---|
| `$cart` | Sama dengan halaman keranjang |
| `$addresses` | Alamat pembeli, alamat utama pertama. Tiap alamat punya `full_address`, `label`, `receiver_name`, `receiver_phone`, `is_default` |
| `$shippingMethods` | Layanan aktif: `id`, `courier`, `service` |
| `$quotes` | `$quotes[alamat_id][layanan_id]` = `null` atau `['zone', 'billable_kg', 'cost', 'etd']` |
| `$weightGram` | Berat total keranjang (gram) |
| `$billableKg` | Berat yang ditagih (kg) |

`$quotes` memuat ongkir untuk SEMUA alamat, sehingga ongkir bisa diperbarui di browser saat
pembeli berganti alamat. Contoh pemakaian ada di skrip pada `checkout/index.blade.php`
(`@json($quotes)`).

## Variabel di halaman pesanan

**`shop/orders/index`**: `$orders` (paginator). Tiap pesanan: `order_code`, `created_at`,
`status`, `grand_total`, `items_count`.

**`shop/orders/show`**: `$order` dengan relasi `items`, `histories`, `shippingMethod`, `shipment`.

| Field | Isi |
|---|---|
| `order_code`, `status`, `created_at` | Kode, status, waktu dibuat |
| `receiver_name`, `receiver_phone`, `shipping_address`, `city_name`, `province_name`, `postal_code` | Alamat saat checkout |
| `total_weight_gram` | Berat total (gram) |
| `subtotal`, `shipping_cost`, `discount_total`, `grand_total` | Rincian biaya (rupiah, bilangan bulat) |
| `shipping_etd` | Estimasi tiba, mis. `2-3 hari` (bisa `null`) |
| `note` | Catatan pembeli (bisa kosong) |
| `items[]` | `product_name`, `price`, `quantity`, `subtotal` |
| `histories[]` | `status`, `note`, `created_at`. Urut dari yang paling lama |
| `shipment` | Bisa `null`. Punya `tracking_number`, `shipped_at`, `delivered_at` |

## Status pesanan

Label dan warna: `\App\Support\OrderStatus::label($status)` dan `::badge($status)`.

| Nilai | Label |
|---|---|
| `pending` | Menunggu pembayaran |
| `paid` | Sudah dibayar |
| `processing` | Diproses |
| `shipped` | Dikirim |
| `completed` | Selesai |
| `cancelled` | Dibatalkan |

## Yang belum ada

Tombol "Bayar sekarang" masih nonaktif. Dibuat di langkah Payment Gateway.
