@extends('layouts.app')
@section('title', 'Beranda - StyleShop')

@php
    // DATA CONTOH sementara. Nanti diganti data dari controller.
    $categories = ['Kaos', 'Kemeja', 'Hoodie', 'Dress'];
    $products = [
        ['name' => 'Kaos Oversize',   'price' => 99000],
        ['name' => 'Hoodie Premium',  'price' => 149000],
        ['name' => 'Jaket Casual',    'price' => 199000],
        ['name' => 'Kemeja Flanel',   'price' => 129000],
    ];
@endphp

@section('content')

{{-- Hero --}}
<section class="bg-brand-50">
    <div class="max-w-6xl mx-auto px-4 py-16 md:py-24">
        <h1 class="text-4xl md:text-5xl font-bold leading-tight">
            Tampil Stylish<br>Setiap Hari
        </h1>
        <p class="mt-4 text-gray-600">Fashion terbaik, harga terjangkau!</p>
        <a href="#" class="inline-block mt-6 bg-brand-600 text-white px-6 py-3
                           rounded-lg font-medium hover:bg-brand-700">
            Belanja Sekarang →
        </a>
    </div>
</section>

{{-- Kategori --}}
<section class="max-w-6xl mx-auto px-4 py-10">
    <div class="flex gap-6 overflow-x-auto">
        @foreach ($categories as $category)
            <a href="#" class="flex-shrink-0 text-center">
                <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center
                            justify-center text-xs text-gray-400">
                    ikon
                </div>
                <span class="text-sm mt-2 block">{{ $category }}</span>
            </a>
        @endforeach
    </div>
</section>

{{-- Produk Terlaris --}}
<section class="max-w-6xl mx-auto px-4">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold">Produk Terlaris</h2>
        <a href="#" class="text-sm text-brand-600 font-medium">Lihat Semua →</a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ($products as $product)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="h-44 bg-gray-200 flex items-center justify-center text-gray-400 text-sm">
                    foto
                </div>
                <div class="p-3">
                    <h3 class="font-semibold text-sm">{{ $product['name'] }}</h3>
                    <p class="text-brand-600 font-bold mt-1">
                        Rp {{ number_format($product['price'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</section>

@endsection