@extends('layouts.app')
@section('title', 'Katalog - StyleShop')

@section('content')
@php
    $placeholder = 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=800&q=80';
@endphp

<section class="bg-brand-50">
    <div class="max-w-6xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold">Koleksi Fashion Terbaik</h1>
        <p class="mt-2 text-gray-600">Temukan produk favoritmu.</p>
    </div>
</section>

<div class="max-w-6xl mx-auto px-4 py-8 md:flex md:gap-8">
    <aside class="md:w-52 mb-6 md:mb-0">
        <h2 class="font-bold text-lg mb-3">Kategori</h2>
        <ul class="flex md:flex-col gap-2 overflow-x-auto">
            <li><a href="{{ route('products.index') }}" class="block px-4 py-2 rounded-lg {{ !$activeCategory ? 'bg-brand-600 text-white' : 'bg-white' }}">Semua</a></li>
            @foreach ($categories as $category)
                <li><a href="{{ route('products.index', ['category' => $category->slug, 'sort' => $activeSort]) }}" class="block px-4 py-2 rounded-lg whitespace-nowrap {{ $activeCategory === $category->slug ? 'bg-brand-600 text-white' : 'bg-white' }}">{{ $category->name }}</a></li>
            @endforeach
        </ul>
    </aside>

    <section class="flex-1">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-500">{{ $products->total() }} produk</p>
            <form method="GET" action="{{ route('products.index') }}" class="text-sm flex items-center gap-2">
                @if ($activeCategory)
                    <input type="hidden" name="category" value="{{ $activeCategory }}">
                @endif
                <label for="sort">Urutkan:</label>
                <select id="sort" name="sort" onchange="this.form.submit()" class="border rounded-lg px-3 py-1.5 bg-white">
                    <option value="newest" @selected($activeSort === 'newest')>Terbaru</option>
                    <option value="price_asc" @selected($activeSort === 'price_asc')>Harga Terendah</option>
                    <option value="price_desc" @selected($activeSort === 'price_desc')>Harga Tertinggi</option>
                </select>
            </form>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($products as $product)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <a href="{{ route('products.show', $product->slug) }}" class="block h-44 overflow-hidden bg-gray-100">
                        @if ($product->primaryImage && $product->primaryImage->path)
                            <img src="{{ \Illuminate\Support\Str::startsWith($product->primaryImage->path, ['http://', 'https://']) ? $product->primaryImage->path : asset('storage/' . ltrim($product->primaryImage->path, '/')) }}"
                                 alt="{{ $product->name }}" loading="lazy"
                                 onerror="this.onerror=null;this.src='{{ $placeholder }}';"
                                 class="h-full w-full object-cover">
                        @else
                            <img src="{{ $placeholder }}" alt="{{ $product->name }}" loading="lazy" class="h-full w-full object-cover">
                        @endif
                    </a>
                    <div class="p-3">
                        <h3 class="font-semibold text-sm">{{ $product->name }}</h3>
                        <p class="text-brand-600 font-bold text-sm mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        <a href="{{ route('products.show', $product->slug) }}" class="inline-block mt-2 text-xs text-brand-600">Lihat produk</a>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">Produk tidak ditemukan.</p>
            @endforelse
        </div>
        <div class="mt-6">{{ $products->links() }}</div>
    </section>
</div>
@endsection
