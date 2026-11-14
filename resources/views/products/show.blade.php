@extends('layouts.app')
@section('title', 'Detail Produk - StyleShop')

@php
    // DATA CONTOH sementara. Nanti diganti data dari controller.
    $product = [
        'name'        => 'Kaos Oversize Premium',
        'price'       => 99000,
        'old_price'   => 149000,
        'rating'      => 4.8,
        'reviews'     => 1245,
        'description' => 'Kaos oversize dengan bahan cotton combed premium, nyaman dipakai seharian.',
        'sizes'       => ['S', 'M', 'L', 'XL'],
    ];
    $discount = floor((1 - $product['price'] / $product['old_price']) * 100);
@endphp

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    {{-- Tombol kembali --}}
    <a href="/preview/katalog" class="inline-flex items-center gap-2 text-sm font-medium
                                      text-gray-600 hover:text-brand-600 mb-6">
        ← Kembali ke Katalog
    </a>

    <div class="md:flex md:gap-10">

        {{-- Galeri foto --}}
        <div class="md:w-1/2 mb-6 md:mb-0">
            <div class="flex gap-3">
                <div class="flex-1 h-80 md:h-[420px] bg-gray-200 rounded-xl flex items-center
                            justify-center text-gray-400 text-sm">
                    foto utama
                </div>
                <div class="flex flex-col gap-3 w-20">
                    @foreach ([1, 2, 3] as $n)
                        <div class="h-24 bg-gray-200 rounded-lg flex items-center justify-center
                                    text-gray-400 text-xs">
                            foto {{ $n }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Informasi produk --}}
        <div class="md:w-1/2">
            <h1 class="text-2xl font-bold">{{ $product['name'] }}</h1>

            <div class="flex items-center gap-3 mt-3">
                <span class="text-3xl font-bold text-brand-600">
                    Rp {{ number_format($product['price'], 0, ',', '.') }}
                </span>
                <span class="text-gray-400 line-through">
                    Rp {{ number_format($product['old_price'], 0, ',', '.') }}
                </span>
                <span class="bg-brand-600 text-white text-xs font-semibold px-2 py-1 rounded">
                    -{{ $discount }}%
                </span>
            </div>

            <p class="mt-2 text-sm">
                <span class="text-yellow-500">★ {{ $product['rating'] }}</span>
                <span class="text-gray-500">({{ number_format($product['reviews'], 0, ',', '.') }} ulasan)</span>
            </p>

            <p class="mt-4 text-gray-600 text-sm leading-relaxed">
                {{ $product['description'] }}
            </p>

            {{-- Ukuran --}}
            <div class="mt-6">
                <p class="font-semibold text-sm mb-2">Ukuran:</p>
                <div class="flex gap-3">
                    @foreach ($product['sizes'] as $size)
                        <label class="cursor-pointer">
                            <input type="radio" name="size" value="{{ $size }}"
                                   class="peer sr-only" {{ $size === 'M' ? 'checked' : '' }}>
                            <span class="block w-12 h-10 leading-10 text-center rounded-lg border
                                         text-sm font-medium bg-white
                                         peer-checked:border-brand-600 peer-checked:bg-brand-50
                                         peer-checked:text-brand-600">
                                {{ $size }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Jumlah --}}
            <div class="mt-6">
                <p class="font-semibold text-sm mb-2">Jumlah:</p>
                <div class="inline-flex items-center border rounded-lg bg-white">
                    <button type="button" id="qty-minus"
                            class="w-10 h-10 text-brand-600 text-lg hover:bg-brand-50">−</button>
                    <input id="qty" type="text" value="1" readonly
                           class="w-12 h-10 text-center border-x focus:outline-none">
                    <button type="button" id="qty-plus"
                            class="w-10 h-10 text-brand-600 text-lg hover:bg-brand-50">+</button>
                </div>
            </div>

            {{-- Tombol aksi --}}
            <div class="mt-8 flex gap-3">
                <button type="button"
                        class="w-12 h-12 flex items-center justify-center rounded-lg border
                               border-brand-600 text-brand-600 hover:bg-brand-50 text-xl">
                    ♡
                </button>
                <button type="button"
                        class="flex-1 h-12 rounded-lg bg-brand-600 hover:bg-brand-700
                               text-white font-semibold text-sm">
                    Tambah ke Keranjang
                </button>
                <a href="#"
                   class="flex-1 h-12 flex items-center justify-center rounded-lg bg-brand-700
                          hover:bg-brand-600 text-white font-semibold text-sm">
                    Beli Sekarang
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Tombol jumlah (+ / −) --}}
<script>
    (function () {
        var qty = document.getElementById('qty');
        document.getElementById('qty-minus').addEventListener('click', function () {
            var v = parseInt(qty.value, 10);
            if (v > 1) { qty.value = v - 1; }
        });
        document.getElementById('qty-plus').addEventListener('click', function () {
            qty.value = parseInt(qty.value, 10) + 1;
        });
    })();
</script>
@endsection