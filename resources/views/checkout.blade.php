@extends('layouts.app')

@section('content')
@php
    $imagePlaceholder = 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=400&q=80';
@endphp

<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">

    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Checkout</h1>
        <p class="mt-1 text-sm text-gray-500">Lengkapi data pengiriman dan pilih metode pembayaran</p>
    </div>

    <form action="{{ route('checkout.store') }}" method="POST" class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        @csrf

        {{-- KOLOM KIRI: Form --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Alamat Pengiriman --}}
            <section class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white">1</span>
                    <h2 class="text-lg font-bold text-gray-900">Alamat Pengiriman</h2>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="receiver_name" class="mb-2 block text-sm font-semibold text-gray-700">Nama Lengkap</label>
                        <input type="text" id="receiver_name" name="receiver_name" value="{{ old('receiver_name', auth()->user()->name) }}" placeholder="Nama penerima" required maxlength="255" autocomplete="name"
                               class="h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-900 placeholder-gray-400 shadow-sm transition focus:border-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-600/10">
                        @error('receiver_name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="receiver_phone" class="mb-2 block text-sm font-semibold text-gray-700">No. HP</label>
                        <input type="tel" id="receiver_phone" name="receiver_phone" value="{{ old('receiver_phone', auth()->user()->phone) }}" placeholder="08xxxxxxxxxx" required maxlength="20" autocomplete="tel" inputmode="tel"
                               class="h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-900 placeholder-gray-400 shadow-sm transition focus:border-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-600/10">
                        @error('receiver_phone') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="province" class="mb-2 block text-sm font-semibold text-gray-700">Provinsi</label>
                        <select id="province" name="province_name" data-old-value="{{ old('province_name') }}" required disabled
                                class="h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-900 shadow-sm transition focus:border-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-600/10 disabled:cursor-wait disabled:bg-gray-50">
                            <option value="">Memuat data...</option>
                        </select>
                        @error('province_name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="city" class="mb-2 block text-sm font-semibold text-gray-700">Kota/Kabupaten</label>
                        <select id="city" name="city_name" data-old-value="{{ old('city_name') }}" required disabled
                                class="h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-900 shadow-sm transition focus:border-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-600/10 disabled:cursor-not-allowed disabled:bg-gray-50">
                            <option value="">Pilih Kota/Kabupaten</option>
                        </select>
                        @error('city_name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="district" class="mb-2 block text-sm font-semibold text-gray-700">Kecamatan</label>
                        <select id="district" name="district_name" data-old-value="{{ old('district_name') }}" required disabled
                                class="h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-900 shadow-sm transition focus:border-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-600/10 disabled:cursor-not-allowed disabled:bg-gray-50">
                            <option value="">Pilih Kecamatan</option>
                        </select>
                        @error('district_name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="village" class="mb-2 block text-sm font-semibold text-gray-700">Desa/Kelurahan</label>
                        <select id="village" name="village_name" data-old-value="{{ old('village_name') }}" required disabled
                                class="h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-900 shadow-sm transition focus:border-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-600/10 disabled:cursor-not-allowed disabled:bg-gray-50">
                            <option value="">Pilih Desa/Kelurahan</option>
                        </select>
                        @error('village_name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="shipping_address" class="mb-2 block text-sm font-semibold text-gray-700">Alamat Lengkap</label>
                        <textarea id="shipping_address" name="shipping_address" rows="3" maxlength="2000" placeholder="Nama jalan, nomor rumah, RT/RW" required
                                  class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 placeholder-gray-400 shadow-sm transition focus:border-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-600/10">{{ old('shipping_address') }}</textarea>
                        @error('shipping_address') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="mb-2 block text-sm font-semibold text-gray-700">Kode Pos <span class="font-normal text-gray-400">(opsional)</span></label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" maxlength="10" autocomplete="postal-code"
                               class="h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-900 placeholder-gray-400 shadow-sm transition focus:border-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-600/10">
                        @error('postal_code') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="shipping_method_id" class="mb-2 block text-sm font-semibold text-gray-700">Layanan Pengiriman</label>
                        <select id="shipping_method_id" name="shipping_method_id" required
                                class="h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-900 shadow-sm transition focus:border-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-600/10">
                            <option value="" data-cost="0">Pilih layanan pengiriman</option>
                            @foreach ($shippingMethods as $method)
                                <option value="{{ $method->id }}" data-cost="{{ $method->cost }}" @selected(old('shipping_method_id') == $method->id)>
                                    {{ $method->courier }} {{ $method->service }} — Rp {{ number_format($method->cost, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('shipping_method_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- Metode Pembayaran --}}
            <section class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white">2</span>
                    <h2 class="text-lg font-bold text-gray-900">Metode Pembayaran</h2>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {{-- Transfer Bank --}}
                    <label class="relative block cursor-pointer">
                        <input type="radio" name="payment_method" value="transfer" class="peer sr-only" required
                               {{ old('payment_method', 'transfer') === 'transfer' ? 'checked' : '' }}>
                        <div class="flex items-start gap-4 rounded-2xl border-2 border-gray-200 bg-white p-5 transition hover:border-gray-300 peer-checked:border-brand-600 peer-checked:bg-brand-600/5 peer-checked:ring-4 peer-checked:ring-brand-600/10 peer-focus-visible:ring-4 peer-focus-visible:ring-brand-600/20">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-700">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900">Transfer Bank</p>
                                <p class="mt-0.5 text-sm text-gray-500">Bayar lewat transfer ke rekening toko</p>
                            </div>
                        </div>
                    </label>

                    {{-- COD --}}
                    <label class="relative block cursor-pointer">
                        <input type="radio" name="payment_method" value="cod" class="peer sr-only"
                               {{ old('payment_method') === 'cod' ? 'checked' : '' }}>
                        <div class="flex items-start gap-4 rounded-2xl border-2 border-gray-200 bg-white p-5 transition hover:border-gray-300 peer-checked:border-brand-600 peer-checked:bg-brand-600/5 peer-checked:ring-4 peer-checked:ring-brand-600/10 peer-focus-visible:ring-4 peer-focus-visible:ring-brand-600/20">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-700">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900">COD</p>
                                <p class="mt-0.5 text-sm text-gray-500">Bayar tunai saat pesanan tiba</p>
                            </div>
                        </div>
                    </label>
                </div>
                @error('payment_method') <p class="mt-3 text-xs text-red-600">{{ $message }}</p> @enderror
            </section>
        </div>

        {{-- KOLOM KANAN: Ringkasan --}}
        <aside class="lg:col-span-1">
            <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-xl shadow-gray-200/60 sm:p-8 lg:sticky lg:top-24">
                <h2 class="text-lg font-bold text-gray-900">Ringkasan Pesanan</h2>

                <ul class="mt-6 max-h-80 divide-y divide-gray-100 overflow-y-auto pr-1">
                    @foreach ($items as $item)
                    @php
                        $product = data_get($item, 'product');
                        $quantity = data_get($item, 'quantity', 0);
                        $productName = data_get($product, 'name', 'Produk');
                        $productPrice = data_get($product, 'price', 0);
                        $imagePath = data_get($product, 'primaryImage.path');
                    @endphp
                    <li class="flex gap-4 py-4 first:pt-0">
                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-gray-100">
                            @if ($imagePath)
                                <img src="{{ \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://']) ? $imagePath : asset('storage/' . ltrim($imagePath, '/')) }}"
                                     alt="{{ $productName }}" onerror="this.onerror=null;this.src='{{ $imagePlaceholder }}';"
                                     class="h-full w-full object-cover">
                            @else
                                <img src="{{ $imagePlaceholder }}" alt="{{ $productName }}" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="line-clamp-2 text-sm font-semibold text-gray-900">{{ $productName }}</p>
                            <p class="mt-0.5 text-xs text-gray-500">{{ $quantity }} &times; Rp {{ number_format($productPrice, 0, ',', '.') }}</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-900">
                            Rp {{ number_format($productPrice * $quantity, 0, ',', '.') }}
                        </p>
                    </li>
                    @endforeach
                </ul>

                <div class="my-6 h-px bg-gray-100"></div>

                <dl class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Subtotal</dt>
                        <dd class="font-semibold text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Ongkos Kirim</dt>
                        <dd id="shipping-cost" class="font-semibold text-gray-900">Pilih layanan</dd>
                    </div>
                </dl>

                <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4">
                    <span class="text-base font-semibold text-gray-900">Total</span>
                    <span id="order-total" data-subtotal="{{ $subtotal }}" class="text-2xl font-bold text-brand-600">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                <button type="submit"
                        class="mt-8 flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 text-base font-bold text-white shadow-lg shadow-brand-600/30 transition hover:bg-brand-700 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-brand-600/30 active:scale-[0.98]">
                    Buat Pesanan
                </button>

                <a href="{{ route('cart.index') }}" class="mt-4 block text-center text-sm font-medium text-gray-500 transition hover:text-brand-600">
                    &larr; Kembali ke keranjang
                </a>
            </div>
        </aside>
    </form>
</div>

<script>
    (function () {
        var apiBase = 'https://www.emsifa.com/api-wilayah-indonesia/api/';
        var province = document.getElementById('province');
        var city = document.getElementById('city');
        var district = document.getElementById('district');
        var village = document.getElementById('village');
        if (!province || !city || !district || !village) return;

        var requests = new Map();

        function createOption(value, label) {
            var option = document.createElement('option');
            option.value = value;
            option.textContent = label;
            return option;
        }

        function resetSelect(select, placeholder) {
            var activeRequest = requests.get(select);
            if (activeRequest) activeRequest.abort();
            requests.delete(select);
            select.replaceChildren(createOption('', placeholder));
            select.disabled = true;
        }

        async function loadOptions(select, url, placeholder, selectedName) {
            var previousRequest = requests.get(select);
            if (previousRequest) previousRequest.abort();

            var controller = new AbortController();
            requests.set(select, controller);
            select.disabled = true;
            select.replaceChildren(createOption('', 'Memuat data...'));

            try {
                var response = await fetch(url, { signal: controller.signal });
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }

                var regions = await response.json();
                if (!Array.isArray(regions)) {
                    throw new Error('Format data wilayah tidak valid.');
                }
                if (requests.get(select) !== controller) return false;

                select.replaceChildren(createOption('', placeholder));
                regions.forEach(function (region) {
                    var option = createOption(region.name, region.name);
                    option.dataset.id = region.id;
                    select.appendChild(option);
                });
                select.disabled = false;

                if (selectedName) {
                    var selectedRegion = regions.find(function (region) {
                        return region.name === selectedName;
                    });
                    if (selectedRegion) select.value = selectedRegion.name;
                }

                requests.delete(select);
                return Boolean(selectedName && select.value);
            } catch (error) {
                if (error.name === 'AbortError' || requests.get(select) !== controller) {
                    return false;
                }

                console.error('Gagal memuat data wilayah:', error);
                select.replaceChildren(createOption('', 'Gagal memuat data. Muat ulang halaman.'));
                select.disabled = true;
                requests.delete(select);
                return false;
            }
        }

        function selectedId(select) {
            var selectedOption = select.options[select.selectedIndex];
            return selectedOption ? selectedOption.dataset.id : '';
        }

        async function restorePreviousAddress() {
            var provinceRestored = await loadOptions(
                province, apiBase + 'provinces.json', 'Pilih Provinsi', province.dataset.oldValue
            );
            if (!provinceRestored) return;

            var cityRestored = await loadOptions(
                city,
                apiBase + 'regencies/' + encodeURIComponent(selectedId(province)) + '.json',
                'Pilih Kota/Kabupaten',
                city.dataset.oldValue
            );
            if (!cityRestored) return;

            var districtRestored = await loadOptions(
                district,
                apiBase + 'districts/' + encodeURIComponent(selectedId(city)) + '.json',
                'Pilih Kecamatan',
                district.dataset.oldValue
            );
            if (!districtRestored) return;

            await loadOptions(
                village,
                apiBase + 'villages/' + encodeURIComponent(selectedId(district)) + '.json',
                'Pilih Desa/Kelurahan',
                village.dataset.oldValue
            );
        }

        province.addEventListener('change', function () {
            resetSelect(city, 'Pilih Kota/Kabupaten');
            resetSelect(district, 'Pilih Kecamatan');
            resetSelect(village, 'Pilih Desa/Kelurahan');
            var provinceId = selectedId(province);
            if (provinceId) {
                loadOptions(city, apiBase + 'regencies/' + encodeURIComponent(provinceId) + '.json', 'Pilih Kota/Kabupaten');
            }
        });

        city.addEventListener('change', function () {
            resetSelect(district, 'Pilih Kecamatan');
            resetSelect(village, 'Pilih Desa/Kelurahan');
            var cityId = selectedId(city);
            if (cityId) {
                loadOptions(district, apiBase + 'districts/' + encodeURIComponent(cityId) + '.json', 'Pilih Kecamatan');
            }
        });

        district.addEventListener('change', function () {
            resetSelect(village, 'Pilih Desa/Kelurahan');
            var districtId = selectedId(district);
            if (districtId) {
                loadOptions(village, apiBase + 'villages/' + encodeURIComponent(districtId) + '.json', 'Pilih Desa/Kelurahan');
            }
        });

        restorePreviousAddress();
    })();

    (function () {
        var method = document.getElementById('shipping_method_id');
        var shipping = document.getElementById('shipping-cost');
        var total = document.getElementById('order-total');
        if (!method || !shipping || !total) return;

        function rupiah(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }

        function updateSummary() {
            var option = method.options[method.selectedIndex];
            var shippingCost = Number(option.dataset.cost || 0);
            var subtotal = Number(total.dataset.subtotal || 0);
            shipping.textContent = method.value ? rupiah(shippingCost) : 'Pilih layanan';
            total.textContent = rupiah(subtotal + shippingCost);
        }

        method.addEventListener('change', updateSummary);
        updateSummary();
    })();
</script>
@endsection