@extends('layouts.app')
@section('title', 'Pesanan Berhasil - StyleShop')

@section('content')
<section class="min-h-[65vh] bg-gradient-to-b from-brand-50 to-gray-50 px-4 py-14 sm:py-20">
    <div class="mx-auto max-w-xl rounded-3xl border border-gray-100 bg-white px-6 py-10 text-center shadow-sm sm:px-10">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
            </svg>
        </div>
        <p class="mt-6 text-sm font-semibold uppercase tracking-widest text-brand-600">Checkout selesai</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900">Terima kasih atas pesananmu!</h1>
        <p class="mt-3 leading-relaxed text-gray-600">Pesananmu berhasil dibuat dan sedang menunggu proses selanjutnya.</p>

        <div class="mt-8 rounded-2xl bg-gray-50 p-5 text-left">
            <div class="flex items-center justify-between gap-4">
                <span class="text-sm text-gray-500">ID Pesanan</span>
                <span class="font-bold text-gray-900">#{{ $order->id }}</span>
            </div>
            <div class="mt-3 flex items-center justify-between gap-4 border-t border-gray-200 pt-3">
                <span class="text-sm text-gray-500">Kode Pesanan</span>
                <span class="text-right font-semibold text-gray-900">{{ $order->order_code }}</span>
            </div>
        </div>

        <a href="{{ route('home') }}" class="mt-8 inline-flex w-full items-center justify-center rounded-xl bg-brand-600 px-6 py-3.5 font-semibold text-white shadow-sm transition hover:bg-brand-700 hover:shadow-md sm:w-auto">
            Kembali ke Beranda
        </a>
    </div>
</section>
@endsection
