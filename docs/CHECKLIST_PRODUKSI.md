# Checklist Sebelum Dipasang di Server Sungguhan

Aplikasi ini aman dijalankan di komputer sendiri untuk tugas dan demo. Sebelum dipakai untuk
penjualan sungguhan, lakukan semua poin di bawah.

## 1. Konfigurasi

- [ ] `APP_ENV=production` dan `APP_DEBUG=false` (jangan pernah menampilkan detail error ke pengunjung)
- [ ] `APP_URL` berisi alamat situs yang sebenarnya dengan **https**
- [ ] `APP_KEY` dibuat baru di server (`php artisan key:generate`), bukan disalin dari komputer lain
- [ ] `php artisan config:cache`, `route:cache`, `view:cache` (atau `php artisan optimize`)
- [ ] Folder `storage` dan `bootstrap/cache` bisa ditulis oleh server web; jalankan `php artisan storage:link`
- [ ] Domain diarahkan ke folder `public`, bukan ke folder utama projek
- [ ] File `.env` **tidak** bisa diunduh dari luar

## 2. Akun dan data

- [ ] Hapus atau jangan jalankan akun seeder (`admin@toko.test`, `pembeli@toko.test`, password `password`)
- [ ] Buat admin sungguhan: `php artisan shop:create-admin email@anda.com`
- [ ] Hapus data uji (pesanan, pelanggan, produk contoh) atau mulai dari database baru
- [ ] Password database bukan `root` tanpa password; buat pengguna database khusus

## 3. Pembayaran Midtrans

- [ ] Akun Midtrans sudah diverifikasi untuk production (data usaha dan rekening)
- [ ] `MIDTRANS_IS_PRODUCTION=true` dengan **Server Key dan Client Key production**
- [ ] Atur **Payment Notification URL** di dashboard Midtrans production ke
      `https://domain-anda/midtrans/notification`
- [ ] Atur **Finish Redirect URL** di dashboard ke halaman pesanan
- [ ] Hapus `MIDTRANS_NOTIFICATION_URL=ngrok` dan `MIDTRANS_VERIFY_SSL=false` bila ada
- [ ] Uji satu transaksi kecil sungguhan dari awal sampai uangnya tercatat

## 4. Email dan antrean

- [ ] Pakai layanan email transaksional (Brevo, Mailgun, Resend, Amazon SES), bukan Gmail pribadi
- [ ] Pekerja antrean berjalan terus: `php artisan queue:work` di bawah pengawas proses (Supervisor)
- [ ] Penjadwal berjalan: cron `* * * * * php artisan schedule:run`, dan di `app/Console/Kernel.php`
      tambahkan `$schedule->command('orders:expire-unpaid')->hourly();`
- [ ] Pantau tabel `failed_jobs` secara berkala

## 5. Keamanan dan operasional

- [ ] Situs berjalan di **HTTPS**; set `SESSION_SECURE_COOKIE=true`
- [ ] Jalankan `composer install --no-dev --optimize-autoloader` dan perbarui paket secara berkala
- [ ] Cadangkan database dan folder `storage/app/public` (gambar produk) secara terjadwal
- [ ] Batasi akses panel `/admin` bila perlu (misalnya hanya dari alamat IP tertentu)
- [ ] Siapkan alamat email dan kontak penjual untuk pembeli yang butuh bantuan pembayaran
