# Kontrak Backend - Frontend: Pesanan (aksi pembeli dan halaman admin)

Melengkapi `KONTRAK_CHECKOUT.md` dan `KONTRAK_PEMBAYARAN.md`. Nama route dan variabel jangan
diubah sepihak. File di `resources/views/shop/` dan `resources/views/admin/` adalah PLACEHOLDER
dan boleh ditimpa desain.

## Alur status

```
pending -> paid -> processing -> shipped -> completed
pending -> cancelled
```

- `pending -> paid`: otomatis, setelah pembayaran Midtrans berhasil.
- `paid -> processing -> shipped`: oleh **admin** (resi wajib diisi saat dikirim).
- `shipped -> completed`: oleh **admin**, atau oleh **pembeli** lewat "Pesanan sudah diterima".
- `pending -> cancelled`: oleh pembeli, admin, atau otomatis saat pembayaran kedaluwarsa. Stok dikembalikan.
- Pesanan yang **sudah dibayar tidak bisa dibatalkan** (tidak ada fitur refund).

Status ditulis ke riwayat (`order->histories`) setiap berubah.

## Aksi pembeli di halaman detail pesanan (`orders.show`)

Semua aksi berupa form POST dengan `@csrf`.

| Aksi | Route | Tampil saat status |
|---|---|---|
| Batalkan pesanan | `route('orders.cancel', $order->order_code)` | `pending` |
| Pesanan sudah diterima | `route('orders.receive', $order->order_code)` | `shipped` |
| Bayar sekarang | `route('payments.show', $order->order_code)` (tautan) | `pending` |
| Cek status pembayaran | `route('payments.check', $order->order_code)` | `pending` |

Hasil aksi dikirim lewat session `success` atau `error` (`@include('shop._flash')`).
Variabel baru di `$order->shipment`: `shipped_at` dan `delivered_at` (bisa `null`).

## Halaman admin (hanya untuk referensi; di luar desain pembeli)

| Halaman | URL | Nama route |
|---|---|---|
| Daftar pesanan | `/admin/orders` | `admin.orders.index` |
| Detail pesanan | `/admin/orders/{id}` | `admin.orders.show` |

Parameter daftar: `status` (nilai status) dan `q` (cari kode, penerima, nama/email pembeli).
