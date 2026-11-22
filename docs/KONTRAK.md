# Kontrak Backend - Frontend: Katalog dan Detail Produk

Dokumen ini menjelaskan data apa saja yang dikirim backend ke halaman, supaya frontend
bisa mendesain tanpa menebak. **Nama route dan nama variabel jangan diubah sepihak.**
Kalau butuh data tambahan, minta ke backend.

## File yang boleh ditimpa frontend

Semua file di `resources/views/shop/` (termasuk `layout.blade.php`) hanya PLACEHOLDER.
Silakan timpa seluruh isinya dengan desain, selama nama variabel di bawah tetap dipakai.

Halaman publik **jangan** memakai `<x-app-layout>` bawaan Breeze, karena layout itu
memanggil `Auth::user()` dan akan error untuk pengunjung yang belum login.

## 1. Katalog produk

- URL: `GET /produk`
- Nama route: `shop.products.index`
- View: `resources/views/shop/products/index.blade.php`

Parameter URL (semua opsional, bisa digabung):

| Parameter | Isi | Contoh |
|---|---|---|
| `q` | Kata kunci nama produk | `/produk?q=kaos` |
| `kategori` | Slug kategori (sub-kategorinya ikut) | `/produk?kategori=elektronik` |
| `sort` | `terbaru` (default), `termurah`, `termahal` | `/produk?sort=termurah` |
| `page` | Nomor halaman (12 produk per halaman) | `/produk?page=2` |

Variabel di view:

| Variabel | Tipe | Keterangan |
|---|---|---|
| `$products` | Paginator | Tampilkan dengan `@foreach`, navigasi dengan `{{ $products->links() }}` |
| `$categories` | Koleksi | Kategori utama aktif, tiap item punya `children` (sub-kategori aktif) |
| `$selectedCategory` | Model atau `null` | Kategori yang sedang dipilih |
| `$keyword` | String | Kata kunci pencarian saat ini (kosong jika tidak ada) |
| `$sort` | String | Urutan yang sedang dipakai |

Hanya produk **aktif** dari kategori **aktif** yang tampil.

## 2. Detail produk

- URL: `GET /produk/{slug}`
- Nama route: `shop.products.show` (contoh: `route('shop.products.show', $product->slug)`)
- View: `resources/views/shop/products/show.blade.php`
- Produk nonaktif atau sudah dihapus menghasilkan halaman 404.

| Variabel | Tipe | Keterangan |
|---|---|---|
| `$product` | Model | Dengan relasi `category` dan `images` |
| `$related` | Koleksi | Maksimal 4 produk lain di kategori yang sama |

## 3. Field yang tersedia

**Produk (`$product`)**

| Field | Keterangan |
|---|---|
| `name`, `slug`, `description` | Teks. `description` boleh kosong |
| `price` | Bilangan bulat rupiah. Format: `number_format($product->price, 0, ',', '.')` |
| `stock` | Jumlah stok. Jika `< 1`, tampilkan "Stok habis" |
| `weight_gram` | Berat dalam gram |
| `category` | Punya `name` dan `slug` |
| `primaryImage` | Gambar utama (bisa `null`). Dipakai di daftar |
| `images` | Semua gambar, terurut. Tiap gambar punya `path` dan `is_primary` |

**Menampilkan gambar**

```blade
<img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="{{ $product->name }}">
```

Selalu cek dulu apakah gambar ada (`@if ($product->primaryImage)`), karena produk bisa
belum punya gambar. Gambar yang diunggah **tidak ikut ke GitHub**; unggah sendiri lewat
halaman admin di komputer masing-masing untuk melihatnya.

## 4. Yang belum ada

Tombol "Tambah ke Keranjang" masih nonaktif. Akan dibuat di langkah Keranjang,
dan kontraknya ditambahkan di dokumen ini.
