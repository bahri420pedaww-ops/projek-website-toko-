{{-- PLACEHOLDER (backend). Variabel: $cart (array)
     $cart['items']          koleksi baris: id, product (bisa null), quantity, unit_price,
                             line_total, max_quantity, problem (teks atau null)
     $cart['subtotal']       total harga baris yang valid (rupiah)
     $cart['total_quantity'] jumlah barang yang valid
     $cart['has_problem']    true jika ada baris bermasalah (stok/produk)
     $cart['is_empty']       true jika keranjang kosong --}}
@extends('shop.layout')

@section('title', 'Keranjang')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Keranjang Belanja</h1>

    @if ($cart['is_empty'])
        <div class="bg-white p-8 rounded-lg shadow-sm text-center">
            <p class="text-gray-600">Keranjangmu masih kosong.</p>
            <a href="{{ route('shop.products.index') }}"
               class="mt-4 inline-block px-5 py-2 bg-gray-800 text-white text-sm font-semibold rounded-md">
                Mulai belanja
            </a>
        </div>
    @else
        <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3">Harga</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($cart['items'] as $line)
                        <tr class="{{ $line->problem ? 'bg-red-50' : '' }}">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($line->product?->primaryImage)
                                        <img src="{{ asset('storage/' . $line->product->primaryImage->path) }}"
                                             alt="{{ $line->product->name }}" class="h-14 w-14 object-cover rounded">
                                    @else
                                        <div class="h-14 w-14 rounded bg-gray-200"></div>
                                    @endif
                                    <div>
                                        @if ($line->product)
                                            <a href="{{ route('shop.products.show', $line->product->slug) }}"
                                               class="font-medium hover:underline">{{ $line->product->name }}</a>
                                        @else
                                            <span class="font-medium text-gray-500">Produk tidak tersedia</span>
                                        @endif
                                        @if ($line->problem)
                                            <p class="text-xs text-red-600">{{ $line->problem }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                Rp {{ number_format($line->unit_price, 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-3">
                                @if ($line->product && $line->max_quantity > 0)
                                    <form method="POST" action="{{ route('cart.update', $line->id) }}"
                                          class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $line->quantity }}"
                                               min="1" max="{{ $line->max_quantity }}"
                                               class="w-20 border-gray-300 rounded-md shadow-sm text-sm">
                                        <button class="text-indigo-600 hover:underline">Perbarui</button>
                                    </form>
                                @else
                                    {{ $line->quantity }}
                                @endif
                            </td>

                            <td class="px-4 py-3 font-medium">
                                Rp {{ number_format($line->line_total, 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('cart.destroy', $line->id) }}"
                                      onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <form method="POST" action="{{ route('cart.clear') }}"
                  onsubmit="return confirm('Kosongkan seluruh keranjang?')">
                @csrf
                @method('DELETE')
                <button class="text-sm text-red-600 hover:underline">Kosongkan keranjang</button>
            </form>

            <div class="bg-white p-6 rounded-lg shadow-sm md:w-80">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Jumlah barang</span>
                    <span>{{ $cart['total_quantity'] }}</span>
                </div>
                <div class="mt-2 flex justify-between font-semibold">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($cart['subtotal'], 0, ',', '.') }}</span>
                </div>

                @if ($cart['has_problem'])
                    <p class="mt-3 text-xs text-red-600">
                        Ada produk bermasalah (stok atau ketersediaan). Perbaiki dulu sebelum checkout.
                    </p>
                @endif

                {{-- Tombol ini diaktifkan di langkah Checkout --}}
                <button type="button" disabled
                        class="mt-4 w-full px-4 py-3 bg-gray-300 text-gray-600 text-sm font-semibold rounded-md cursor-not-allowed">
                    Lanjut ke Checkout (segera hadir)
                </button>
            </div>
        </div>
    @endif
@endsection
