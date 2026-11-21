{{-- PLACEHOLDER (backend). Variabel: $product (dengan category dan images), $related (koleksi) --}}
@extends('shop.layout')

@section('title', $product->name)

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- Galeri gambar --}}
        <div>
            @php $main = $product->images->firstWhere('is_primary', true) ?? $product->images->first(); @endphp

            @if ($main)
                <img src="{{ asset('storage/' . $main->path) }}" alt="{{ $product->name }}"
                     class="w-full rounded-lg object-cover">

                @if ($product->images->count() > 1)
                    <div class="mt-3 grid grid-cols-4 gap-2">
                        @foreach ($product->images as $image)
                            <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $product->name }}"
                                 class="h-20 w-full rounded object-cover">
                        @endforeach
                    </div>
                @endif
            @else
                <div class="h-80 w-full rounded-lg bg-gray-200 flex items-center justify-center text-gray-500">
                    Tidak ada gambar
                </div>
            @endif
        </div>

        {{-- Informasi produk --}}
        <div>
            <a href="{{ route('shop.products.index', ['kategori' => $product->category->slug]) }}"
               class="text-sm text-gray-500 hover:underline">{{ $product->category->name }}</a>

            <h1 class="mt-1 text-2xl font-semibold">{{ $product->name }}</h1>
            <p class="mt-2 text-2xl font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>

            <p class="mt-3 text-sm">
                @if ($product->stock > 0)
                    <span class="text-green-700">Stok tersedia: {{ $product->stock }}</span>
                @else
                    <span class="font-semibold text-red-600">Stok habis</span>
                @endif
                <span class="text-gray-500"> &middot; Berat {{ number_format($product->weight_gram, 0, ',', '.') }} gram</span>
            </p>

            {{-- Tombol ini akan diaktifkan di langkah Keranjang --}}
            <button type="button" disabled
                    class="mt-5 px-6 py-3 bg-gray-300 text-gray-600 text-sm font-semibold rounded-md cursor-not-allowed">
                Tambah ke Keranjang (segera hadir)
            </button>

            @if ($product->description)
                <div class="mt-6 text-sm leading-relaxed">
                    <h2 class="font-semibold mb-1">Deskripsi</h2>
                    {!! nl2br(e($product->description)) !!}
                </div>
            @endif
        </div>
    </div>

    @if ($related->isNotEmpty())
        <section class="mt-12">
            <h2 class="font-semibold text-lg mb-4">Produk lain di kategori ini</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($related as $item)
                    @include('shop.products._card', ['product' => $item])
                @endforeach
            </div>
        </section>
    @endif
@endsection
