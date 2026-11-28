# Panduan Uji Penerimaan (UAT)

Jalankan seluruh skenario ini sebelum demo atau pengumpulan, lalu isi kolom **Hasil** dengan
LULUS atau GAGAL. Tabel ini juga bisa disalin ke laporan.

**Persiapan:** `php artisan migrate:fresh --seed`, `npm run dev` berjalan, `php artisan queue:work` berjalan,
`MAIL_MAILER` dan Midtrans sandbox sudah diatur. Gunakan email nyata untuk akun pembeli.
(Peringatan: `migrate:fresh` menghapus SEMUA data. Jangan dijalankan di data yang ingin dipertahankan.)

## A. Akun dan hak akses

| No | Skenario | Langkah | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| A1 | Pendaftaran pembeli | Daftar di `/register` dengan email nyata | Akun dibuat; email sambutan masuk; admin menerima "Pelanggan baru" | |
| A2 | Login | Masuk dengan akun yang benar | Berhasil masuk | |
| A3 | Login salah | Masuk dengan password salah | Pesan kredensial tidak cocok | |
| A4 | Pembeli ke halaman admin | Sebagai pembeli, buka `/admin` | Halaman 403 | |
| A5 | Tamu ke keranjang | Tanpa login, buka `/keranjang` | Diarahkan ke login | |
| A6 | Halaman tidak ada | Buka `/halaman-tidak-ada` | Halaman 404 buatan sendiri | |

## B. Katalog dan keranjang

| No | Skenario | Langkah | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| B1 | Katalog | Buka `/produk` | Produk aktif tampil dengan gambar, harga | |
| B2 | Cari dan filter | Cari nama; filter kategori; ubah urutan | Hasil sesuai | |
| B3 | Produk nonaktif | Nonaktifkan produk di admin, buka detailnya | 404 | |
| B4 | Tambah ke keranjang | Tambah produk jumlah 2 | Masuk keranjang, header "Keranjang (2)" | |
| B5 | Produk sama dua kali | Tambah produk yang sama lagi | Jumlah bertambah, tidak ada baris ganda | |
| B6 | Melebihi stok | Isi jumlah lebih besar dari stok | Disesuaikan dengan stok + peringatan | |
| B7 | Ubah dan hapus | Ubah jumlah, hapus item | Subtotal ikut berubah | |

## C. Alamat dan checkout

| No | Skenario | Langkah | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| C1 | Tambah alamat dengan peta | Klik peta di `/akun/alamat/tambah` | Kota, provinsi, kode pos terisi; bisa diubah manual | |
| C2 | Alamat utama | Tambah alamat kedua, jadikan utama | Hanya satu alamat utama | |
| C3 | Checkout tanpa alamat | Akun tanpa alamat buka `/checkout` | Diarahkan ke tambah alamat | |
| C4 | Ongkir dinamis | Ganti alamat di checkout | Ongkir tiap kurir berubah sesuai tujuan dan berat | |
| C5 | Buat pesanan | Pilih alamat dan kurir, klik Buat Pesanan | Pesanan "Menunggu pembayaran"; stok berkurang; keranjang kosong; email dibuat masuk | |
| C6 | Klik ganda | Klik Buat Pesanan berkali-kali cepat | Hanya satu pesanan | |
| C7 | Stok tidak cukup | Keranjang 3, admin ubah stok jadi 1, checkout | Ditolak dengan pesan stok | |
| C8 | Snapshot | Ubah harga produk dan alamat setelah pesan | Pesanan lama tidak berubah | |

## D. Pembayaran

| No | Skenario | Langkah | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| D1 | Bayar sukses | Bayar lewat popup Midtrans sandbox | Pesanan "Sudah dibayar" otomatis; email dibayar masuk | |
| D2 | Pembaruan otomatis | Bayar dari tab lain, biarkan halaman pesanan terbuka | Status berubah sendiri tanpa klik | |
| D3 | Token dipakai ulang | Buka halaman bayar berkali-kali | Hanya satu baris pembayaran | |
| D4 | Webhook palsu | Kirim notifikasi dengan signature salah | Ditolak 403 | |
| D5 | Notifikasi ganda | `payments:simulate ... settlement` dua kali | Tidak ada perubahan atau email ganda | |
| D6 | Kedaluwarsa | `payments:simulate ... expire` | Pesanan dibatalkan; stok kembali; email batal masuk | |

## E. Pengelolaan pesanan (admin)

| No | Skenario | Langkah | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| E1 | Daftar pesanan | Buka `/admin/orders`, filter dan cari | Hasil sesuai | |
| E2 | Proses | Pesanan lunas, klik Proses pesanan | Status "Diproses" | |
| E3 | Kirim tanpa resi | Klik Kirim pesanan dengan resi kosong | Ditolak | |
| E4 | Kirim dengan resi | Isi resi, kirim | Status "Dikirim"; email "dikirim" berisi resi | |
| E5 | Kurir mengantar | Klik "Kurir sedang mengantar" | Email masuk; tombol hanya bisa sekali | |
| E6 | Selesai | Pembeli klik "Pesanan sudah diterima" | Status "Selesai"; email selesai masuk | |
| E7 | Batalkan belum dibayar | Admin atau pembeli batalkan pesanan menunggu | Dibatalkan, stok kembali, email batal masuk | |
| E8 | Batalkan sudah dibayar | Cari tombol batal pada pesanan lunas | Tidak tersedia | |

## F. Laporan dan lain-lain

| No | Skenario | Langkah | Hasil yang diharapkan | Hasil |
|---|---|---|---|---|
| F1 | Laporan | Buka `/admin/reports` | Angka cocok dengan database; pesanan belum dibayar tidak dihitung | |
| F2 | Ekspor CSV | Unduh CSV | Terbuka di Excel dengan kolom rapi | |
| F3 | Stok menipis | Ubah stok produk jadi 2 | Muncul di daftar stok menipis | |
| F4 | Keamanan pesanan | Akun lain buka pesanan orang lain | 404 | |
| F5 | Email gagal | Salahkan MAIL_PASSWORD lalu buat pesanan | Pesanan tetap sukses; pekerjaan masuk `failed_jobs` | |
