@extends('layouts.app')
@section('title', 'Katalog - StyleShop')

@php
    // DATA CONTOH sementara. Nanti diganti data dari controller.
    $categories = ['Semua', 'Kaos', 'Kemeja', 'Hoodie', 'Dress', 'Jaket', 'Celana', 'Rok', 'Aksesoris'];
    $activeCategory = 'Semua';

    $products = [
        ['name' => 'Kaos Polos',        'price' => 99000,  'rating' => 4.8],
        ['name' => 'Hoodie Oversize',   'price' => 149000, 'rating' => 4.8],
        ['name' => 'Jaket Denim',       'price' => 199000, 'rating' => 4.7],
        ['name' => 'Dress Wanita',      'price' => 179000, 'rating' => 4.8],
        ['name' => 'Kemeja Flanel',     'price' => 129000, 'rating' => 4.6],
        ['name' => 'Celana Chino',      'price' => 159000, 'rating' => 4.7],
        ['name' => 'Rok Plisket',       'price' => 119000, 'rating' => 4.5],
        ['name' => 'Topi Baseball',     'price' => 59000,  'rating' => 4.9],
    ];
@endphp

@section('content')

{{-- Banner --}}
<section class="bg-brand-50">
    <div class="max-w-6xl mx-auto px-4 py-10 md:py-14">
        <h1 class="text-3xl md:text-4xl font-bold leading-tight">
            Koleksi Fashion<br>Terbaik
        </h1>
        <p class="mt-2 text-gray-600">Untuk Gaya Hidup Modern</p>
        <div class="mt-4 w-12 h-1 rounded bg-brand-600"></div>
    </div>
</section>

<div class="max-w-6xl mx-auto px-4 py-8 md:flex md:gap-8">

    {{-- Kategori: daftar di kiri pada layar lebar, deret geser pada layar kecil --}}
    <aside class="md:w-52 md:flex-shrink-0 mb-6 md:mb-0">
        <h2 class="font-bold text-lg mb-3">Kategori</h2>
        <ul class="flex md:flex-col gap-2 overflow-x-auto md:overflow-visible pb-2 md:pb-0">
            @foreach ($categories as $category)
                <li class="flex-shrink-0">
                    <a href="#"
                       class="block px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap
                              {{ $category === $activeCategory
                                    ? 'bg-brand-600 text-white'
                                    : 'bg-white text-gray-700 hover:bg-brand-50' }}">
                        {{ $category }}
                    </a>
                </li>
            @endforeach
        </ul>
    </aside>

    {{-- Daftar produk --}}
    <section class="flex-1">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-500">{{ count($products) }} produk</p>
            <label class="text-sm flex items-center gap-2">
                Urutkan:
                <select class="border rounded-lg px-3 py-1.5 bg-white text-sm">
                    <option>Terpopuler</option>
                    <option>Terbaru</option>
                    <option>Harga Terendah</option>
                    <option>Harga Tertinggi</option>
                </select>
            </label>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($products as $product)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <a href="/preview/produk" class="h-44 bg-gray-200 flex items-center justify-center text-gray-400 text-sm">
                        foto
                    </a>
                    <div class="p-3">
                        <h3 class="font-semibold text-sm">{{ $product['name'] }}</h3>
                        <p class="text-brand-600 font-bold text-sm mt-1">
                            Rp {{ number_format($product['price'], 0, ',', '.') }}
                        </p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-yellow-500">★ {{ $product['rating'] }}</span>
                            <button type="button"
                                    class="bg-brand-600 hover:bg-brand-700 text-white text-xs
                                           px-3 py-1.5 rounded-lg">
                                + Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>

@endsection