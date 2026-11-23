@extends('layouts.app')
@section('title', 'Keranjang - StyleShop')

@php
    // DATA CONTOH sementara. Nanti diganti data dari controller.
    $items = [
        ['name' => 'Kaos Oversize',   'variant' => 'Hitam', 'price' => 99000,  'qty' => 1],
        ['name' => 'Hoodie Premium',  'variant' => 'Cream', 'price' => 149000, 'qty' => 1],
    ];
    $shipping = 20000;
@endphp

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    {{-- Judul --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Keranjang Belanja</h1>
        <button type="button" id="clear-all"
                class="text-sm font-medium text-brand-600 hover:text-brand-700">
            Hapus Semua
        </button>
    </div>

    {{-- Daftar item --}}
    <div id="cart-list" class="space-y-4">
        @foreach ($items as $item)
            <div class="cart-item bg-white rounded-xl shadow-sm p-4 flex gap-4"
                 data-price="{{ $item['price'] }}">

                <div class="w-24 h-24 flex-shrink-0 bg-gray-200 rounded-lg flex items-center
                            justify-center text-gray-400 text-xs">
                    foto
                </div>

                <div class="flex-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-semibold">{{ $item['name'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $item['variant'] }}</p>
                            <p class="text-sm font-bold text-brand-600 mt-1">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </p>
                        </div>
                        <button type="button" class="btn-remove text-gray-400 hover:text-brand-600"
                                aria-label="Hapus item">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 7l-1 12a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7m5 4v6m4-6v6M9 7V4h6v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center justify-between mt-3">
                        <div class="inline-flex items-center border rounded-lg bg-white">
                            <button type="button" class="btn-minus w-9 h-9 text-brand-600 hover:bg-brand-50">−</button>
                            <span class="qty w-10 text-center text-sm">{{ $item['qty'] }}</span>
                            <button type="button" class="btn-plus w-9 h-9 text-brand-600 hover:bg-brand-50">+</button>
                        </div>
                        <p class="line-total font-bold text-sm"></p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Keranjang kosong --}}
    <div id="cart-empty" class="hidden bg-white rounded-xl shadow-sm p-10 text-center">
        <p class="text-gray-500">Keranjang belanjamu kosong.</p>
        <a href="/preview/katalog"
           class="inline-block mt-4 bg-brand-600 hover:bg-brand-700 text-white
                  px-5 py-2 rounded-lg text-sm font-medium">
            Mulai Belanja
        </a>
    </div>

    {{-- Ringkasan --}}
    <div id="cart-summary" class="mt-6 bg-white rounded-xl shadow-sm p-5">
        <div class="flex justify-between text-sm py-1">
            <span class="text-gray-600">Subtotal</span>
            <span id="subtotal" class="font-medium"></span>
        </div>
        <div class="flex justify-between text-sm py-1">
            <span class="text-gray-600">Ongkos Kirim</span>
            <span id="shipping" class="font-medium" data-amount="{{ $shipping }}"></span>
        </div>
        <div class="flex justify-between py-3 mt-2 border-t">
            <span class="font-semibold">Total</span>
            <span id="total" class="text-xl font-bold text-brand-600"></span>
        </div>

        <a href="/preview/checkout"
           class="mt-2 w-full h-12 flex items-center justify-center rounded-lg bg-brand-600
                  hover:bg-brand-700 text-white font-semibold text-sm">
            Checkout →
        </a>
    </div>
</div>

{{-- Hitung ulang jumlah, subtotal, dan total --}}
<script>
    (function () {
        var list    = document.getElementById('cart-list');
        var empty   = document.getElementById('cart-empty');
        var summary = document.getElementById('cart-summary');

        function rupiah(n) {
            return 'Rp ' + n.toLocaleString('id-ID');
        }

        function update() {
            var rows = list.querySelectorAll('.cart-item');
            var subtotal = 0;

            rows.forEach(function (row) {
                var price = parseInt(row.dataset.price, 10);
                var qty   = parseInt(row.querySelector('.qty').textContent, 10);
                subtotal += price * qty;
                row.querySelector('.line-total').textContent = rupiah(price * qty);
            });

            var shippingEl = document.getElementById('shipping');
            var shipping = rows.length > 0 ? parseInt(shippingEl.dataset.amount, 10) : 0;

            document.getElementById('subtotal').textContent = rupiah(subtotal);
            shippingEl.textContent = rupiah(shipping);
            document.getElementById('total').textContent = rupiah(subtotal + shipping);

            empty.classList.toggle('hidden', rows.length > 0);
            summary.classList.toggle('hidden', rows.length === 0);
        }

        list.addEventListener('click', function (e) {
            var row = e.target.closest('.cart-item');
            if (!row) { return; }
            var qtyEl = row.querySelector('.qty');
            var qty = parseInt(qtyEl.textContent, 10);

            if (e.target.closest('.btn-plus')) {
                qtyEl.textContent = qty + 1;
            } else if (e.target.closest('.btn-minus')) {
                if (qty > 1) { qtyEl.textContent = qty - 1; }
            } else if (e.target.closest('.btn-remove')) {
                row.remove();
            }
            update();
        });

        document.getElementById('clear-all').addEventListener('click', function () {
            list.innerHTML = '';
            update();
        });

        update();
    })();
</script>
@endsection