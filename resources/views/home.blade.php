@extends('layouts.app')
@section('title', 'Beranda - StyleShop')

@php
    $categories = [
        ['name' => 'Kaos', 'icon' => '👕'],
        ['name' => 'Kemeja', 'icon' => '👔'],
        ['name' => 'Hoodie', 'icon' => '🧥'],
        ['name' => 'Dress', 'icon' => '👗'],
    ];
    
    $products = [
        [
            'name' => 'Kaos Oversize Hitam',   
            'price' => 99000,
            // Pria memakai kaos hitam polos
            'image' => 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=600&q=80' 
        ],
        [
            'name' => 'Hoodie Premium Cream',  
            'price' => 149000,
            // Pria memakai hoodie warna krem/cream
            'image' => 'https://images.unsplash.com/photo-1578587018452-892bacefd3f2?w=600&q=80' 
        ],
        [
            'name' => 'Jaket Casual Pria',    
            'price' => 199000,
            // Pria memakai jaket (kulit/casual)
            'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=600&q=80' 
        ],
        [
            'name' => 'Kemeja Flanel Kotak',   
            'price' => 129000,
            // Pria memakai kemeja flanel motif kotak-kotak
            'image' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=600&q=80' 
        ],
    ];
@endphp

@section('content')

{{-- Hero Section --}}
<section class="bg-brand-50">
    <div class="max-w-6xl mx-auto px-4 py-16 md:py-24">
        <div class="max-w-2xl">
            <h1 class="text-4xl md:text-5xl font-bold leading-tight text-gray-900">
                Tampil Stylish<br>Setiap Hari
            </h1>
            <p class="mt-4 text-lg text-gray-600">Fashion terbaik, harga terjangkau untuk menemani aktivitasmu.</p>
            <a href="/preview/katalog" class="inline-block mt-8 bg-brand-600 text-white px-8 py-3.5 rounded-lg font-semibold hover:bg-brand-700 transition duration-300 shadow-md hover:shadow-lg">
                Belanja Sekarang →
            </a>
        </div>
    </div>
</section>

{{-- Kategori Section --}}
<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="flex gap-6 overflow-x-auto pb-4 scrollbar-hide">
        @foreach ($categories as $category)
            <a href="/preview/katalog" class="flex-shrink-0 text-center group cursor-pointer">
                <div class="w-20 h-20 rounded-full bg-white border border-gray-100 shadow-sm flex items-center justify-center text-3xl group-hover:border-brand-500 group-hover:shadow-md transition-all duration-300">
                    {{ $category['icon'] }}
                </div>
                <span class="text-sm font-medium text-gray-700 mt-3 block group-hover:text-brand-600 transition-colors">
                    {{ $category['name'] }}
                </span>
            </a>
        @endforeach
    </div>
</section>

{{-- Produk Terlaris Section --}}
<section class="max-w-6xl mx-auto px-4 pb-20">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Produk Terlaris</h2>
        <a href="/preview/katalog" class="text-sm font-semibold text-brand-600 hover:text-brand-700 hover:underline">
            Lihat Semua →
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($products as $product)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-xl transition-all duration-300 flex flex-col">
                
                {{-- Gambar Produk --}}
                <div class="relative h-64 overflow-hidden bg-gray-100">
                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    
                    {{-- Tombol Keranjang (Muncul saat di-hover di Desktop) --}}
                    <div class="absolute inset-x-0 bottom-0 p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 hidden md:block">
                        <button class="w-full bg-gray-900 text-white font-medium py-2.5 rounded-lg hover:bg-brand-600 transition-colors shadow-lg">
                            + Tambah ke Keranjang
                        </button>
                    </div>
                </div>

                {{-- Info Produk --}}
                <div class="p-5 flex flex-col flex-grow">
                    <h3 class="font-semibold text-gray-800 text-lg truncate">{{ $product['name'] }}</h3>
                    <p class="text-brand-600 font-bold text-xl mt-2">
                        Rp {{ number_format($product['price'], 0, ',', '.') }}
                    </p>
                    
                    {{-- Tombol Mobile (Karena di HP tidak ada hover) --}}
                    <div class="mt-4 md:hidden">
                        <button class="w-full bg-gray-100 text-gray-800 font-medium py-2 rounded-lg hover:bg-brand-600 hover:text-white transition-colors">
                            + Keranjang
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

@endsection