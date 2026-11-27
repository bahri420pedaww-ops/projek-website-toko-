{{-- PLACEHOLDER (backend). Variabel: $order dengan relasi items, histories, shippingMethod, shipment, payments.
     order: order_code, status, created_at, receiver_name, receiver_phone, shipping_address,
            city_name, province_name, postal_code, total_weight_gram, subtotal, shipping_cost,
            shipping_etd (bisa null), discount_total, grand_total, note
     items: product_name, price, quantity, subtotal (dihitung)
     histories: status, note, created_at
     payments: gateway_order_id, status, payment_type, paid_at, created_at
     shipment: tracking_number, shipped_at, delivered_at (bisa null)
     Aksi pembeli (POST, @csrf): orders.cancel (status pending) | orders.receive (status shipped)
     Pembaruan otomatis (status pending): skrip di bagian bawah memanggil route('payments.status')
     tiap 5 detik lalu memuat ulang halaman begitu status berubah. --}}
@extends('shop.layout')

@section('title', 'Pesanan ' . $order->order_code)

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold">{{ $order->order_code }}</h1>
            <p class="text-sm text-gray-500">Dibuat {{ $order->created_at->format('d M Y H:i') }}</p>
        </div>
        <span class="px-3 py-1 rounded text-sm font-semibold {{ \App\Support\OrderStatus::badge($order->status) }}">
            {{ \App\Support\OrderStatus::label($order->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            {{-- Isi pesanan --}}
            <section class="bg-white shadow-sm rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3">Jumlah</th>
                            <th class="px-4 py-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="px-4 py-3">{{ $item->product_name }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ $item->quantity }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            {{-- Pengiriman --}}
            <section class="bg-white p-6 rounded-lg shadow-sm text-sm">
                <h2 class="font-semibold mb-2">Pengiriman</h2>
                <p class="font-medium">{{ $order->receiver_name }} <span class="text-gray-600">({{ $order->receiver_phone }})</span></p>
                <p class="text-gray-600">
                    {{ $order->shipping_address }}, {{ $order->city_name }}, {{ $order->province_name }}
                    {{ $order->postal_code }}
                </p>
                <p class="mt-2 text-gray-600">
                    Layanan: {{ $order->shippingMethod->courier }} {{ $order->shippingMethod->service }}
                    @if ($order->shipping_etd)
                        &middot; Estimasi {{ $order->shipping_etd }}
                    @endif
                </p>
                @if ($order->total_weight_gram > 0)
                    <p class="text-gray-600">Berat: {{ number_format($order->total_weight_gram, 0, ',', '.') }} gram</p>
                @endif
                @if ($order->shipment?->tracking_number)
                    <p class="text-gray-600">No. resi: <span class="font-medium">{{ $order->shipment->tracking_number }}</span>
                        @if ($order->shipment->shipped_at)
                            <span class="text-xs text-gray-400">(dikirim {{ $order->shipment->shipped_at->format('d M Y') }})</span>
                        @endif
                    </p>
                @endif
                @if ($order->note)
                    <p class="mt-2 text-gray-600">Catatan: {{ $order->note }}</p>
                @endif
            </section>

            {{-- Pembayaran --}}
            @if ($order->payments->isNotEmpty())
                <section class="bg-white p-6 rounded-lg shadow-sm text-sm">
                    <h2 class="font-semibold mb-3">Pembayaran</h2>
                    <ul class="space-y-3">
                        @foreach ($order->payments->sortByDesc('id') as $payment)
                            <li class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <p class="font-medium">{{ $payment->gateway_order_id }}</p>
                                    <p class="text-xs text-gray-500">
                                        @if ($payment->payment_type)
                                            {{ str_replace('_', ' ', $payment->payment_type) }} &middot;
                                        @endif
                                        {{ $payment->paid_at
                                            ? 'Dibayar ' . $payment->paid_at->format('d M Y H:i')
                                            : 'Dibuat ' . $payment->created_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ \App\Support\PaymentStatus::badge($payment->status) }}">
                                    {{ \App\Support\PaymentStatus::label($payment->status) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- Riwayat status --}}
            <section class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="font-semibold mb-3">Riwayat Status</h2>
                <ol class="space-y-3 text-sm">
                    @foreach ($order->histories->reverse() as $history)
                        <li>
                            <p class="font-medium">{{ \App\Support\OrderStatus::label($history->status) }}</p>
                            @if ($history->note)
                                <p class="text-gray-600">{{ $history->note }}</p>
                            @endif
                            <p class="text-xs text-gray-400">{{ $history->created_at?->format('d M Y H:i') }}</p>
                        </li>
                    @endforeach
                </ol>
            </section>
        </div>

        {{-- Ringkasan biaya dan aksi --}}
        <aside>
            <div class="bg-white p-6 rounded-lg shadow-sm space-y-2 text-sm">
                <h2 class="font-semibold mb-1">Rincian Biaya</h2>
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ongkos kirim</span>
                    <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                @if ($order->discount_total > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Diskon</span>
                        <span>- Rp {{ number_format($order->discount_total, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-semibold border-t pt-3">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                </div>

                @if ($order->status === 'pending')
                    <a href="{{ route('payments.show', $order->order_code) }}"
                       class="mt-4 block w-full text-center px-4 py-3 bg-gray-800 text-white text-sm font-semibold rounded-md hover:bg-gray-700">
                        Bayar sekarang
                    </a>

                    <form method="POST" action="{{ route('payments.check', $order->order_code) }}">
                        @csrf
                        <button class="mt-2 w-full px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                            Cek status pembayaran
                        </button>
                    </form>
                    <p class="text-xs text-gray-400 text-center">Status diperbarui otomatis setelah kamu membayar.</p>

                    <form method="POST" action="{{ route('orders.cancel', $order->order_code) }}"
                          onsubmit="return confirm('Batalkan pesanan ini?')">
                        @csrf
                        <button class="mt-2 w-full px-4 py-2 text-red-700 text-sm font-medium rounded-md hover:bg-red-50">
                            Batalkan pesanan
                        </button>
                    </form>
                @elseif ($order->status === 'shipped')
                    <form method="POST" action="{{ route('orders.receive', $order->order_code) }}"
                          onsubmit="return confirm('Pesanan sudah kamu terima dengan baik?')">
                        @csrf
                        <button class="mt-4 w-full px-4 py-3 bg-gray-800 text-white text-sm font-semibold rounded-md hover:bg-gray-700">
                            Pesanan sudah diterima
                        </button>
                    </form>
                @endif
            </div>
        </aside>
    </div>

    {{-- Pembaruan otomatis selama pesanan menunggu pembayaran --}}
    @if ($order->status === 'pending')
        <script>
            (function () {
                const statusUrl = @json(route('payments.status', $order->order_code));
                const orderUrl = @json(route('orders.show', $order->order_code));
                const intervalMs = 5000;
                const maxChecks = 120; // sekitar 10 menit
                let checks = 0;
                let timer = null;

                async function check() {
                    if (document.hidden) return; // tidak memeriksa saat tab tidak terlihat

                    checks++;

                    try {
                        const res = await fetch(statusUrl, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin'
                        });

                        if (res.ok) {
                            const data = await res.json();

                            if (data.status !== 'pending') {
                                clearInterval(timer);
                                window.location.href = orderUrl + '?diperbarui=1';
                                return;
                            }
                        }
                    } catch (e) { /* abaikan; dicoba lagi pada giliran berikutnya */ }

                    if (checks >= maxChecks) clearInterval(timer);
                }

                timer = setInterval(check, intervalMs);
                // Begitu pembeli kembali ke tab ini (misalnya setelah membayar di tab lain), langsung periksa
                document.addEventListener('visibilitychange', function () { if (!document.hidden) check(); });
            })();
        </script>
    @endif
@endsection
