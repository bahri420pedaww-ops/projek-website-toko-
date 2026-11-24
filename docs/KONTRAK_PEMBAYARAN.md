# Kontrak Backend - Frontend: Pembayaran (Midtrans)

Melengkapi `docs/KONTRAK_CHECKOUT.md`. Nama route dan variabel jangan diubah sepihak.
Semua file di `resources/views/shop/` adalah PLACEHOLDER dan boleh ditimpa desain.

## Alur untuk pembeli

1. Detail pesanan (`orders.show`) berstatus `pending` menampilkan tombol **Bayar sekarang**
   (tautan ke `route('payments.show', $order->order_code)`).
2. Halaman pembayaran memuat Snap.js Midtrans dan membuka popup pembayaran.
3. Selesai atau ditutup, pembeli dikembalikan ke `route('orders.show', $order->order_code)`
   dengan `?cek=1` (backend langsung memeriksa status ke Midtrans).
4. Tombol **Cek status pembayaran** (form POST ke `payments.check`) bisa dipakai kapan saja.

## Route

| Halaman | Method | URL | Nama route |
|---|---|---|---|
| Halaman bayar | GET | `/pesanan/{order_code}/bayar` | `payments.show` |
| Cek status | POST | `/pesanan/{order_code}/cek-pembayaran` | `payments.check` |
| Webhook Midtrans | POST | `/midtrans/notification` | `payments.notification` (bukan untuk pembeli) |

Semua halaman pembeli wajib login. Webhook dipanggil oleh server Midtrans dan jangan diubah.

## Halaman pembayaran (`shop/payments/show`)

| Variabel | Isi |
|---|---|
| `$order` | Pesanan yang dibayar |
| `$payment` | Percobaan pembayaran; `snap_token` dipakai untuk `snap.pay()` |
| `$clientKey` | Client Key Midtrans (aman ditampilkan di browser) |
| `$snapJsUrl` | Alamat skrip Snap.js (sandbox atau production) |

Bagian skrip di `payments/show.blade.php` (`<script src=$snapJsUrl data-client-key=...>` dan
pemanggilan `window.snap.pay(token, {...})`) harus dipertahankan. Tampilan di sekitarnya bebas.

## Detail pesanan (`shop/orders/show`)

Variabel tambahan: `$order->payments` (percobaan pembayaran, bisa lebih dari satu).

| Field pembayaran | Isi |
|---|---|
| `gateway_order_id` | Kode transaksi di Midtrans |
| `status` | `pending`, `settlement`, `expire`, `cancel`, `deny`, `failed` |
| `payment_type` | Metode, mis. `bank_transfer`, `gopay`, `qris` (bisa kosong) |
| `paid_at` | Waktu dibayar (bisa `null`) |

Label dan warna: `\App\Support\PaymentStatus::label($status)` dan `::badge($status)`.

## Aturan penting

- Pesanan berubah menjadi `paid` **hanya oleh backend** setelah Midtrans mengonfirmasi.
  Frontend tidak pernah mengirim status "sudah dibayar".
- Pesanan `pending` yang tidak dibayar dalam batas waktu (bawaan 24 jam) otomatis menjadi
  `cancelled` dan stok dikembalikan.
- Pembayaran yang ditolak (`deny`/`failed`) tidak membatalkan pesanan; pembeli bisa menekan
  "Bayar sekarang" lagi (backend membuat percobaan baru).
