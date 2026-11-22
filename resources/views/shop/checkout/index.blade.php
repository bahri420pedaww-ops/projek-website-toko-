{{-- PLACEHOLDER (backend). Variabel:
     $cart                lihat shop/cart/index
     $addresses           alamat pembeli (alamat utama pertama)
     $shippingMethods     layanan aktif: id, courier, service
     $quotes              ongkir per kombinasi: $quotes[alamat_id][layanan_id] = null (tidak melayani)
                          atau ['zone', 'billable_kg', 'cost', 'etd']
     $weightGram          berat total keranjang (gram)
     $billableKg          berat yang ditagih (kg, dibulatkan ke atas, minimal 1)
     Form POST ke route('checkout.store') dengan field: address_id, shipping_method_id, note --}}
@extends('shop.layout')

@section('title', 'Checkout')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Checkout</h1>

    <form method="POST" action="{{ route('checkout.store') }}" id="checkout_form"
          class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf

        <div class="lg:col-span-2 space-y-6">

            {{-- Alamat pengiriman --}}
            <section class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-semibold">Alamat Pengiriman</h2>
                    <a href="{{ route('addresses.create') }}" class="text-sm text-indigo-600 hover:underline">
                        Tambah alamat
                    </a>
                </div>

                @error('address_id') <p class="mb-2 text-sm text-red-600">{{ $message }}</p> @enderror

                @foreach ($addresses as $address)
                    <label class="flex items-start gap-3 border rounded-md p-3 mb-2 cursor-pointer">
                        <input type="radio" name="address_id" value="{{ $address->id }}" class="mt-1"
                               @checked(old('address_id', $addresses->first()->id) == $address->id)>
                        <span class="text-sm">
                            <span class="font-semibold">{{ $address->label }}</span>
                            @if ($address->is_default)
                                <span class="ms-1 text-xs font-semibold text-green-700 bg-green-50 px-2 py-0.5 rounded">Utama</span>
                            @endif
                            <br>
                            <span class="font-medium">{{ $address->receiver_name }}</span>
                            <span class="text-gray-600">({{ $address->receiver_phone }})</span><br>
                            <span class="text-gray-600">{{ $address->full_address }}</span>
                        </span>
                    </label>
                @endforeach
            </section>

            {{-- Pengiriman --}}
            <section class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="font-semibold mb-1">Layanan Pengiriman</h2>
                <p class="mb-3 text-xs text-gray-500">
                    Berat total {{ number_format($weightGram, 0, ',', '.') }} gram
                    (ditagih {{ $billableKg }} kg, dibulatkan ke atas, minimal 1 kg).
                    Ongkir mengikuti kota tujuan alamat yang dipilih.
                </p>

                @error('shipping_method_id') <p class="mb-2 text-sm text-red-600">{{ $message }}</p> @enderror

                @foreach ($shippingMethods as $method)
                    <label class="flex items-center justify-between border rounded-md p-3 mb-2 cursor-pointer"
                           data-method="{{ $method->id }}">
                        <span class="flex items-center gap-3 text-sm">
                            <input type="radio" name="shipping_method_id" value="{{ $method->id }}"
                                   @checked(old('shipping_method_id') == $method->id)>
                            <span>
                                {{ $method->courier }} {{ $method->service }}
                                <span class="js-etd block text-xs text-gray-500"></span>
                            </span>
                        </span>
                        <span class="js-cost text-sm font-medium">-</span>
                    </label>
                @endforeach
            </section>

            {{-- Catatan --}}
            <section class="bg-white p-6 rounded-lg shadow-sm">
                <label for="note" class="block font-semibold mb-2">Catatan untuk penjual (opsional)</label>
                <textarea id="note" name="note" rows="2" maxlength="255"
                          class="block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('note') }}</textarea>
                @error('note') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </section>
        </div>

        {{-- Ringkasan --}}
        <aside>
            <div class="bg-white p-6 rounded-lg shadow-sm space-y-3">
                <h2 class="font-semibold">Ringkasan Pesanan</h2>

                <ul class="text-sm divide-y divide-gray-100">
                    @foreach ($cart['items'] as $line)
                        <li class="py-2 flex justify-between gap-3">
                            <span>{{ $line->product->name }} &times; {{ $line->quantity }}</span>
                            <span class="whitespace-nowrap">Rp {{ number_format($line->line_total, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal</span>
                    <span>Rp {{ number_format($cart['subtotal'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Ongkos kirim</span>
                    <span id="shipping_total">-</span>
                </div>
                <div class="flex justify-between font-semibold border-t pt-3">
                    <span>Total</span>
                    <span id="grand_total">-</span>
                </div>

                <button id="submit_btn"
                        class="w-full px-4 py-3 bg-gray-800 text-white text-sm font-semibold rounded-md hover:bg-gray-700 disabled:bg-gray-300 disabled:cursor-not-allowed">
                    Buat Pesanan
                </button>
                <p class="text-xs text-gray-500">
                    Total akhir dihitung ulang oleh server saat pesanan dibuat.
                </p>
            </div>
        </aside>
    </form>

    <script>
        (function () {
            const subtotal = {{ (int) $cart['subtotal'] }};
            const quotes = @json($quotes);
            const fmt = (n) => 'Rp ' + new Intl.NumberFormat('id-ID').format(n);

            const shippingEl = document.getElementById('shipping_total');
            const totalEl = document.getElementById('grand_total');
            const submitBtn = document.getElementById('submit_btn');

            function selectedAddressId() {
                const r = document.querySelector('input[name="address_id"]:checked');
                return r ? r.value : null;
            }

            function refresh() {
                const forAddress = quotes[selectedAddressId()] || {};
                let firstAvailable = null;

                document.querySelectorAll('[data-method]').forEach(function (row) {
                    const q = forAddress[row.dataset.method];
                    const radio = row.querySelector('input[type="radio"]');
                    const costEl = row.querySelector('.js-cost');
                    const etdEl = row.querySelector('.js-etd');

                    if (q) {
                        radio.disabled = false;
                        radio.dataset.cost = q.cost;
                        costEl.textContent = fmt(q.cost);
                        etdEl.textContent = q.etd ? 'Estimasi ' + q.etd : '';
                        row.classList.remove('opacity-50');
                        if (!firstAvailable) firstAvailable = radio;
                    } else {
                        radio.disabled = true;
                        radio.checked = false;
                        delete radio.dataset.cost;
                        costEl.textContent = 'Tidak melayani tujuan ini';
                        etdEl.textContent = '';
                        row.classList.add('opacity-50');
                    }
                });

                if (!document.querySelector('input[name="shipping_method_id"]:checked') && firstAvailable) {
                    firstAvailable.checked = true;
                }
                updateTotals();
            }

            function updateTotals() {
                const sel = document.querySelector('input[name="shipping_method_id"]:checked');
                const cost = sel ? parseInt(sel.dataset.cost, 10) : 0;
                shippingEl.textContent = sel ? fmt(cost) : '-';
                totalEl.textContent = fmt(subtotal + cost);
                submitBtn.disabled = !sel;
            }

            document.querySelectorAll('input[name="address_id"]').forEach(function (r) {
                r.addEventListener('change', refresh);
            });
            document.querySelectorAll('input[name="shipping_method_id"]').forEach(function (r) {
                r.addEventListener('change', updateTotals);
            });
            refresh();

            // Cegah pesanan ganda akibat klik dua kali
            document.getElementById('checkout_form').addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Memproses...';
            });
        })();
    </script>
@endsection
