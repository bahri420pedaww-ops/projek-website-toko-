@extends('layouts.app')
@section('title', $product->name . ' - StyleShop')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 md:py-12">
    <nav class="text-sm text-gray-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-brand-600">Beranda</a>
        <span class="mx-2">/</span>
        <a href="{{ route('products.index') }}" class="hover:text-brand-600">Katalog</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800">{{ $product->name }}</span>
    </nav>

    <div class="flex flex-col md:flex-row gap-8 lg:gap-12">
        <div class="w-full md:w-1/2">
            @if ($product->images->isNotEmpty())
                <img src="{{ asset('storage/' . ($product->primaryImage?->path ?? $product->images->first()->path)) }}" alt="{{ $product->name }}" class="w-full rounded-2xl object-cover">
            @else
                <div class="aspect-square bg-gray-100 rounded-2xl flex items-center justify-center text-gray-400">Foto produk</div>
            @endif
        </div>
        <div class="w-full md:w-1/2 flex flex-col justify-center">
            <p class="text-sm text-gray-500">{{ $product->category?->name ?? 'Tanpa kategori' }}</p>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">{{ $product->name }}</h1>
            <p class="text-3xl font-bold text-brand-600 my-4">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            <div class="border-t border-gray-100 py-6">
                <h2 class="text-sm font-semibold uppercase mb-3">Deskripsi Produk</h2>
                <p class="text-gray-600 leading-relaxed">{{ $product->description ?: 'Belum ada deskripsi produk.' }}</p>
            </div>
            <p class="mb-6 text-sm {{ $product->stock > 0 ? 'text-green-700' : 'text-red-600' }}">
                {{ $product->stock > 0 ? 'Stok tersedia (' . $product->stock . ')' : 'Stok habis' }}
            </p>
            @if ($product->stock > 0)
                <form action="{{ route('cart.store') }}" method="POST" class="border-t border-gray-100 pt-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <label for="quantity" class="block text-sm font-medium mb-2">Jumlah</label>
                    <div class="flex gap-4">
                        <input id="quantity" type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="{{ min($product->stock, 100) }}" required class="w-28 border border-gray-200 rounded-xl px-4 py-3 text-center focus:border-brand-600 focus:ring-2 focus:ring-brand-100">
                        <button type="submit" class="flex-1 bg-brand-600 text-white font-semibold py-3 rounded-xl shadow-sm transition hover:bg-brand-700 hover:shadow-md">Tambah ke Keranjang</button>
                    </div>
                    @error('quantity')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
