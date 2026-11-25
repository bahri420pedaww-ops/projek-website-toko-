@extends('layouts.app')
@section('title', 'Checkout - StyleShop')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-700">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <form action="{{ route('checkout.store') }}" method="POST" class="space-y-4 rounded-xl bg-white p-6 shadow-sm">
        @csrf
        <label class="block">Nama penerima
            <input name="receiver_name" value="{{ old('receiver_name', auth()->user()->name) }}" required maxlength="255" class="mt-1 w-full rounded-lg border p-3">
        </label>
        <label class="block">Nomor telepon
            <input name="receiver_phone" value="{{ old('receiver_phone', auth()->user()->phone) }}" required maxlength="20" class="mt-1 w-full rounded-lg border p-3">
        </label>
        <label class="block">Alamat lengkap
            <textarea name="shipping_address" required maxlength="2000" class="mt-1 w-full rounded-lg border p-3">{{ old('shipping_address') }}</textarea>
        </label>
        <div class="grid gap-4 sm:grid-cols-3">
            <label class="block">Kota
                <input name="city_name" value="{{ old('city_name') }}" required maxlength="255" class="mt-1 w-full rounded-lg border p-3">
            </label>
            <label class="block">Provinsi
                <input name="province_name" value="{{ old('province_name') }}" required maxlength="255" class="mt-1 w-full rounded-lg border p-3">
            </label>
            <label class="block">Kode pos
                <input name="postal_code" value="{{ old('postal_code') }}" maxlength="10" class="mt-1 w-full rounded-lg border p-3">
            </label>
        </div>
        <label class="block">Layanan pengiriman
            <select name="shipping_method_id" required class="mt-1 w-full rounded-lg border p-3">
                <option value="">Pilih layanan</option>
                @foreach ($shippingMethods as $method)
                    <option value="{{ $method->id }}" @selected(old('shipping_method_id') == $method->id)>{{ $method->courier }} {{ $method->service }} — Rp {{ number_format($method->cost, 0, ',', '.') }}</option>
                @endforeach
            </select>
        </label>
        <label class="block">Catatan (opsional)
            <textarea name="note" maxlength="2000" class="mt-1 w-full rounded-lg border p-3">{{ old('note') }}</textarea>
        </label>
        <button type="submit" class="w-full rounded-lg bg-brand-600 py-3 font-semibold text-white">Buat Pesanan</button>
    </form>
</div>
@endsection
