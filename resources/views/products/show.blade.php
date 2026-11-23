@extends('layouts.app')
@section('title', 'Detail Produk - StyleShop')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 md:py-12">
    
    <!-- Breadcrumb (Jejak navigasi) -->
    <nav class="flex text-sm text-gray-500 mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="/" class="hover:text-brand-600 transition-colors">Beranda</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="/preview/katalog" class="ml-1 hover:text-brand-600 md:ml-2 transition-colors">Katalog</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-gray-800 font-medium md:ml-2">Kaos Oversize Hitam</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Konten Utama: Kiri Gambar, Kanan Info -->
    <div class="flex flex-col md:flex-row gap-8 lg:gap-12">
        
        <!-- KIRI: Gambar Produk -->
        <div class="w-full md:w-1/2">
            <div class="bg-gray-100 rounded-2xl overflow-hidden shadow-sm border border-gray-100 group">
                <!-- Gambar disamakan dengan data dummy di home -->
                <img src="https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=1000&q=80" 
                     alt="Kaos Oversize Hitam" 
                     class="w-full h-auto object-cover aspect-square group-hover:scale-105 transition-transform duration-500">
            </div>
        </div>

        <!-- KANAN: Info & Beli -->
        <div class="w-full md:w-1/2 flex flex-col justify-center">
            
            <!-- Judul & Harga -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Kaos Oversize Hitam</h1>
            <p class="text-3xl font-bold text-brand-600 mb-6">Rp 99.000</p>
            
            <!-- Deskripsi Singkat -->
            <div class="mb-8 border-t border-gray-100 pt-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Deskripsi Produk</h3>
                <p class="text-gray-600 leading-relaxed">
                    Kaos oversize berbahan katun premium yang sangat nyaman dipakai sehari-hari. 
                    Potongan longgar (oversize) memberikan gaya kasual yang stylish. Jahitan rapi dan 
                    bahan adem, cocok untuk cuaca Indonesia.
                </p>
            </div>

            <!-- Info Stok & Ukuran -->
            <div class="flex items-center gap-6 mb-8 text-sm bg-gray-50 p-4 rounded-xl border border-gray-100">
                <div class="flex items-center gap-2 text-gray-600">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span>
                    Stok Tersedia (12)
                </div>
                <div class="w-px h-4 bg-gray-300"></div>
                <div class="flex items-center gap-2 text-gray-600">
                    <span class="font-medium text-gray-900">Ukuran:</span> M, L, XL
                </div>
            </div>

            <!-- Form Tambah ke Keranjang -->
            <form action="/cart/add" method="POST" class="border-t border-gray-100 pt-8">
                @csrf
                <input type="hidden" name="product_id" value="1">
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Input Jumlah -->
                    <div class="w-full sm:w-32">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                        <input type="number" name="qty" value="1" min="1" max="12" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 text-center focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition">
                    </div>
                    
                    <!-- Tombol Beli -->
                    <div class="flex-grow flex flex-col justify-end">
                        <button type="submit" class="w-full bg-brand-600 text-white font-semibold py-3.5 rounded-lg hover:bg-brand-700 transition duration-300 shadow-md hover:shadow-lg flex justify-center items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Tambah ke Keranjang
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection