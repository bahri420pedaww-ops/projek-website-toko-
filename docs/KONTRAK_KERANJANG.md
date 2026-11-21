# Kontrak Backend - Frontend: Keranjang

Melengkapi `docs/KONTRAK.md`. Nama route, nama field form, dan nama variabel jangan
diubah sepihak. Kalau butuh data tambahan, minta ke backend.

Semua file di `resources/views/shop/` adalah PLACEHOLDER dan boleh ditimpa desain.

## Aturan umum

- Keranjang **wajib login**. Tamu yang membuka `/keranjang` otomatis diarahkan ke halaman login.
- Tamu tidak bisa menambah produk: di halaman detail, tampilkan tombol "Masuk untuk membeli"
  (lihat `shop/cart/_add_form.blade.php`).
- Semua aksi (tambah, ubah, hapus) memakai **form POST** dengan `@csrf`. Untuk PATCH dan
  DELETE tambahkan `@method('PATCH')` atau `@method('DELETE')`.
- Setelah aksi, backend mengirim pesan lewat session: `success`, `warning`, `error`.
  Tampilkan dengan `@include('shop._flash')` atau desain sendiri.
- Stok **tidak dikunci** saat produk masuk keranjang. Stok baru dikunci saat checkout.

## Route

| Aksi | Method | URL | Nama route | Field form |
|---|---|---|---|---|
| Lihat keranjang | GET | `/keranjang` | `cart.index` | - |
| Tambah produk | POST | `/keranjang` | `cart.store` | `product_id`, `quantity` (opsional, default 1) |
| Ubah jumlah | PATCH | `/keranjang/{id}` | `cart.update` | `quantity` |
| Hapus satu item | DELETE | `/keranjang/{id}` | `cart.destroy` | - |
| Kosongkan keranjang | DELETE | `/keranjang` | `cart.clear` | - |

`{id}` adalah `$line->id` (ID baris keranjang), bukan ID produk.

Contoh: `route('cart.update', $line->id)`.

## Halaman keranjang (`shop/cart/index.blade.php`)

Satu variabel: `$cart` (array).

| Kunci | Isi |
|---|---|
| `$cart['items']` | Koleksi baris keranjang (lihat tabel di bawah) |
| `$cart['subtotal']` | Total harga semua baris yang valid, bilangan bulat rupiah |
| `$cart['total_quantity']` | Jumlah barang yang valid |
| `$cart['has_problem']` | `true` jika ada baris bermasalah |
| `$cart['is_empty']` | `true` jika keranjang kosong |

Setiap baris (`$line`):

| Field | Isi |
|---|---|
| `$line->id` | ID baris keranjang (untuk update dan hapus) |
| `$line->product` | Model produk dengan `primaryImage` dan `category`. Bisa `null` jika produk sudah dihapus |
| `$line->quantity` | Jumlah yang dipesan |
| `$line->unit_price` | Harga satuan (rupiah) |
| `$line->line_total` | `unit_price x quantity`. Bernilai 0 jika baris bermasalah |
| `$line->max_quantity` | Batas jumlah yang boleh dipesan (stok atau 99). Pakai untuk atribut `max` |
| `$line->problem` | Teks masalah (stok habis, produk tidak tersedia, dan sebagainya) atau `null` |

Baris bermasalah (`problem` tidak `null`) tidak dihitung ke subtotal. Tandai dengan warna
atau ikon peringatan. Selama `has_problem` bernilai `true`, tombol checkout sebaiknya
tidak aktif (checkout akan memblokirnya juga di sisi backend).

## Tombol tambah ke keranjang (halaman detail)

Gunakan `@include('shop.cart._add_form', ['product' => $product])`, atau buat sendiri dengan
form POST ke `route('cart.store')` berisi `product_id` dan `quantity`.

## Jumlah barang di header

```blade
@auth
    {{ (int) Auth::user()->cartItems()->sum('quantity') }}
@endauth
```

## Yang belum ada

Tombol "Lanjut ke Checkout" masih nonaktif. Akan dibuat di langkah Checkout,
dan kontraknya ditambahkan di dokumen terpisah.
