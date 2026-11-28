# Website Penjualan Toko (Darsu Store)

Aplikasi toko online berbasis **Laravel 10** dengan pembayaran **Midtrans**, ongkos kirim dari berat dan
kota tujuan, notifikasi email, dan panel admin.

> Pembagian kerja: **Backend**: (isi nama) | **Frontend**: (isi nama)

## Fitur

**Pembeli**
- Daftar dan masuk akun, katalog produk (cari, filter kategori, urutkan), detail produk
- Keranjang belanja, alamat tersimpan (dengan pilihan lokasi di peta), checkout
- Ongkos kirim otomatis dari berat dan kota tujuan
- Pembayaran Midtrans (Snap), status pembayaran diperbarui otomatis
- Riwayat pesanan, pembatalan, dan konfirmasi pesanan diterima
- Email notifikasi: akun dibuat, pesanan dibuat, dibayar, dikirim, kurir mengantar, selesai, dibatalkan

**Admin** (`/admin`)
- Kelola kategori dan produk (banyak gambar), pelanggan dan alamatnya
- Kelola pesanan: proses, kirim (nomor resi), tandai selesai, batalkan
- Layanan kurir dan tarif ongkir per zona
- Laporan penjualan: ringkasan, per hari, produk terlaris, stok menipis, ekspor CSV

## Teknologi

Laravel 10, PHP 8.1+, MySQL/MariaDB, Blade + Tailwind CSS (Vite), Laravel Breeze, Midtrans Snap,
Leaflet + OpenStreetMap, antrean database.

## Cara menjalankan (Laragon/Windows)

```bash
git clone <url-repository>
cd Website-Penjualan-Toko
composer install
copy .env.example .env
php artisan key:generate
```

1. Buat database kosong `toko_online` (HeidiSQL), lalu cek `DB_*` di `.env`.
2. Jalankan:

```bash
php artisan migrate --seed
php artisan storage:link
npm install
npm run dev
```

3. Buka `http://website-penjualan-toko.test` (atau jalankan `php artisan serve` lalu buka `http://127.0.0.1:8000`).

Email, antrean, dan Midtrans bersifat opsional untuk menjalankan aplikasi. Cara mengaktifkannya ada di bagian bawah.

## Akun uji (dibuat oleh seeder)

| Peran | Email | Password |
|---|---|---|
| Admin | `admin@toko.test` | `password` |
| Pembeli | `pembeli@toko.test` | `password` |

Akun ini hanya untuk pengembangan. Alamat `.test` adalah alamat fiktif, jadi email ke sana tidak dikirim.
Untuk menguji email, daftar akun baru dengan email nyata. Buat admin sungguhan dengan
`php artisan shop:create-admin email@anda.com`.

## Mengaktifkan email

1. Di `.env`, ubah `MAIL_MAILER=smtp`, isi `MAIL_USERNAME`, `MAIL_FROM_ADDRESS`, dan `MAIL_PASSWORD`
   (App Password Gmail: Akun Google, Keamanan, Verifikasi 2 Langkah, Sandi aplikasi).
2. Isi `SHOP_ADMIN_EMAIL` dengan email admin yang nyata.
3. `php artisan config:clear`, uji dengan `php artisan shop:test-mail emailanda@gmail.com`.
4. Jalankan pekerja antrean di terminal terpisah: `php artisan queue:work`.

## Mengaktifkan pembayaran Midtrans (sandbox)

1. Buat akun di `dashboard.sandbox.midtrans.com`, ambil Server Key dan Client Key (Settings, Access Keys).
2. Isi `MIDTRANS_SERVER_KEY` dan `MIDTRANS_CLIENT_KEY` di `.env`, lalu `php artisan config:clear`.
3. Status pembayaran diperbarui otomatis oleh halaman pesanan. Untuk webhook di komputer lokal gunakan ngrok
   dan `MIDTRANS_NOTIFICATION_URL=ngrok`, lalu cek dengan `php artisan midtrans:webhook-url --ping`.
4. Meniru notifikasi tanpa internet: `php artisan payments:simulate INV-... settlement`.

## Perintah yang berguna

| Perintah | Fungsi |
|---|---|
| `php artisan queue:work` | Mengirim email dari antrean |
| `php artisan orders:expire-unpaid` | Batalkan pesanan yang tidak dibayar dan kembalikan stok |
| `php artisan shop:test-mail email` | Uji pengaturan email |
| `php artisan shop:create-admin email` | Buat akun admin |
| `php artisan payments:simulate INV-... settlement` | Simulasi notifikasi Midtrans (sandbox) |
| `php artisan midtrans:webhook-url --ping` | Cek alamat webhook ngrok |

## Alur pesanan

```
pending -> paid -> processing -> shipped -> completed
pending -> cancelled
```

Pesanan yang sudah dibayar tidak dapat dibatalkan (tidak ada fitur refund). Stok dikurangi saat pesanan dibuat
dan dikembalikan saat pesanan dibatalkan atau pembayaran kedaluwarsa.

## Struktur penting

| Lokasi | Isi |
|---|---|
| `app/Services` | Logika bisnis: keranjang, checkout, ongkir, pembayaran, status pesanan, laporan |
| `app/Http/Controllers/Admin` | Panel admin |
| `app/Http/Controllers/Shop`, `Account` | Halaman pembeli |
| `app/Notifications`, `Listeners`, `Events` | Email notifikasi |
| `routes/shop.php`, `account.php`, `payment.php`, `admin.php` | Route per area |
| `resources/views` | Tampilan (placeholder backend, didesain ulang oleh frontend) |
| `docs/` | Kontrak backend-frontend, panduan uji, checklist produksi |

## Keamanan

- `.env` tidak boleh di-commit (sudah di `.gitignore`). Server Key Midtrans dan App Password bersifat rahasia.
- Signature setiap notifikasi Midtrans diverifikasi. Halaman admin dilindungi middleware `admin`.
- Proses checkout memakai transaksi database dan penguncian baris agar stok tidak terjual ganda.
- Sebelum dipasang di server sungguhan, ikuti `docs/CHECKLIST_PRODUKSI.md`.
