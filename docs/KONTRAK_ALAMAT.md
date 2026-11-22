# Kontrak Backend - Frontend: Alamat Pembeli (versi dengan peta)

Melengkapi `docs/KONTRAK.md` dan `docs/KONTRAK_KERANJANG.md`. Nama route, nama field form,
dan nama variabel jangan diubah sepihak. Semua file di `resources/views/account/` adalah
PLACEHOLDER dan boleh ditimpa desain.

## Aturan umum

- Semua halaman alamat **wajib login** (tamu diarahkan ke halaman login).
- Semua aksi memakai form dengan `@csrf`. Untuk PUT, PATCH, DELETE tambahkan
  `@method('PUT')`, `@method('PATCH')`, atau `@method('DELETE')`.
- Pesan dari backend lewat session `success`, `warning`, `error` (`@include('shop._flash')`).
- Setiap pembeli punya **satu alamat utama**. Alamat pertama otomatis menjadi utama.
  Menghapus alamat utama memindahkan status utama ke alamat terbaru yang tersisa.
- Maksimal 10 alamat per akun.
- Tautan ke halaman alamat untuk menu akun: `route('addresses.index')`.
- **Tidak ada pilihan kota.** Provinsi, kota, kecamatan, dan kode pos adalah kolom teks yang
  diisi manual oleh pembeli, atau otomatis dari peta.

## Route

| Aksi | Method | URL | Nama route |
|---|---|---|---|
| Daftar alamat | GET | `/akun/alamat` | `addresses.index` |
| Form tambah | GET | `/akun/alamat/tambah` | `addresses.create` |
| Simpan baru | POST | `/akun/alamat` | `addresses.store` |
| Form ubah | GET | `/akun/alamat/{id}/ubah` | `addresses.edit` |
| Simpan perubahan | PUT | `/akun/alamat/{id}` | `addresses.update` |
| Jadikan utama | PATCH | `/akun/alamat/{id}/utama` | `addresses.default` |
| Hapus | DELETE | `/akun/alamat/{id}` | `addresses.destroy` |

## Field form (tambah dan ubah)

| Field | Aturan |
|---|---|
| `label` | Wajib, maksimal 50 karakter |
| `receiver_name` | Wajib |
| `receiver_phone` | Wajib, nomor HP Indonesia (`08...`, `628...`, `+628...`). Spasi dan tanda hubung dibuang otomatis |
| `province` | Wajib, teks bebas |
| `city` | Wajib, teks bebas (kota atau kabupaten) |
| `district` | Opsional, kecamatan |
| `postal_code` | Opsional, tepat 5 angka |
| `street_address` | Wajib, minimal 10 karakter |
| `latitude`, `longitude` | Opsional, tetapi harus dikirim berpasangan. Angka desimal dari peta |
| `is_default` | Opsional. Kirim `0` (hidden) dan `1` (checkbox) seperti di `_form.blade.php` |

Pesan error validasi tersedia lewat `$errors` (`@error('nama_field')`).

## Variabel di view

| View | Variabel |
|---|---|
| `account/addresses/index` | `$addresses`: koleksi, alamat utama paling atas |
| `account/addresses/create` | tidak ada |
| `account/addresses/edit` | `$address` |

Field tambahan pada setiap alamat (`$address`):

| Field | Isi |
|---|---|
| `$address->full_address` | Alamat satu baris: jalan, kecamatan, kota, provinsi, kode pos |
| `$address->map_url` | Tautan OpenStreetMap, atau `null` kalau pembeli tidak menandai lokasi |

## Peta

Form memakai **Leaflet + OpenStreetMap** (gratis, tanpa API key), dimuat dari CDN di akhir
`_form.blade.php`. Perilakunya:

- Klik peta atau geser pin: mengisi `latitude`, `longitude`, lalu mengisi provinsi, kota,
  kecamatan, kode pos, dan alamat jalan otomatis.
- Kotak "Cari": mencari tempat lalu memindahkan pin.
- Tombol "Lokasi saya": memakai GPS browser. Hanya jalan di `localhost`/HTTPS dan butuh izin pengguna.
- Semua kolom alamat tetap bisa diisi dan diubah manual. Peta bersifat opsional.
- Peta butuh koneksi internet (peta dasar dan pencarian alamat dari OpenStreetMap).

Kalau mengganti desain form, pertahankan `id` elemen ini karena skrip peta memakainya:
`address_map`, `map_search`, `map_search_btn`, `map_locate_btn`, `map_status`, `latitude`,
`longitude`, `province`, `city`, `district`, `postal_code`, `street_address`.

## Yang belum ada

Pemilihan alamat saat checkout dibuat di langkah Checkout, dan kontraknya ditambahkan
di dokumen terpisah.
