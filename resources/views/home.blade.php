@extends('layouts.app')
@section('title', 'Beranda - StyleShop')

@section('content')

{{-- Hero Section --}}
<section class="bg-brand-50">
    <div class="max-w-6xl mx-auto px-4 py-16 md:py-24">
        <div class="max-w-2xl">
            <h1 class="text-4xl md:text-5xl font-bold leading-tight text-gray-900">
                Tampil Stylish<br>Setiap Hari
            </h1>
            <p class="mt-4 text-lg text-gray-600">Fashion terbaik, harga terjangkau untuk menemani aktivitasmu.</p>
            <a href="#katalog" class="inline-block mt-8 bg-brand-600 text-white px-8 py-3.5 rounded-lg font-semibold hover:bg-brand-700 transition duration-300 shadow-md hover:shadow-lg">
                Belanja Sekarang →
            </a>
        </div>
    </div>
</section>

{{-- Produk Section --}}
<section id="katalog" class="max-w-6xl mx-auto px-4 py-12 pb-20">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Produk Terbaru</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        {{-- Looping data asli dari Database --}}
        @forelse ($products as $product)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-xl transition-all duration-300 flex flex-col">
                
                {{-- Gambar Produk --}}
                <div class="relative h-64 overflow-hidden bg-gray-100">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">Foto Produk</div>
                    @endif
                    
                    {{-- Tombol Keranjang Desktop --}}
                    <div class="absolute inset-x-0 bottom-0 p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 hidden md:block">
                        <form action="{{ route('cart.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1"> {{-- Wajib 'quantity' sesuai controller --}}
                            <button type="submit" class="w-full bg-gray-900 text-white font-medium py-2.5 rounded-lg hover:bg-brand-600 transition-colors shadow-lg">
                                + Tambah ke Keranjang
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Info Produk --}}
                <div class="p-5 flex flex-col flex-grow">
                    <h3 class="font-semibold text-gray-800 text-lg truncate">
                        <a href="{{ route('products.show', $product->slug ?? $product->id) }}" class="hover:text-brand-600 transition">
                            {{ $product->name }}
                        </a>
                    </h3>
                    <p class="text-brand-600 font-bold text-xl mt-2">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>
                    
                    {{-- Tombol Mobile --}}
                    <div class="mt-4 md:hidden">
                        <form action="{{ route('cart.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full bg-gray-100 text-gray-800 font-medium py-2 rounded-lg hover:bg-brand-600 hover:text-white transition-colors">
                                + Keranjang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-4 text-center py-10 text-gray-500">
                Belum ada produk yang ditambahkan ke database.
            </div>
        @endforelse
    </div>
</section>

@endsection