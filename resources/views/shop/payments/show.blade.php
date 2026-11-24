{{-- PLACEHOLDER (backend). Variabel: $order, $payment (snap_token), $clientKey, $snapJsUrl.
     Halaman ini memuat Snap.js Midtrans dan membuka popup pembayaran.
     Kontrak: snap.pay(token) dipanggil dengan $payment->snap_token; setelah selesai/ditutup,
     arahkan pembeli ke route('orders.show', $order->order_code) (tambahkan ?cek=1 agar status
     langsung diperiksa ke Midtrans). --}}
@extends('shop.layout')

@section('title', 'Pembayaran ' . $order->order_code)

@section('content')
    <div class="max-w-lg mx-auto bg-white p-8 rounded-lg shadow-sm text-center space-y-4">
        <h1 class="text-xl font-semibold">Pembayaran</h1>

        <p class="text-sm text-gray-600">Pesanan <span class="font-medium">{{ $order->order_code }}</span></p>
        <p class="text-3xl font-bold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</p>

        <button id="pay_btn" type="button"
                class="w-full px-6 py-3 bg-gray-800 text-white text-sm font-semibold rounded-md hover:bg-gray-700">
            Bayar sekarang
        </button>

        <p class="text-xs text-gray-500">
            Jendela pembayaran Midtrans akan terbuka. Kalau tertutup, klik tombol di atas untuk membukanya lagi.
        </p>

        <a href="{{ route('orders.show', $order->order_code) }}" class="block text-sm text-gray-600 hover:underline">
            Kembali ke pesanan
        </a>
    </div>

    <script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        (function () {
            const token = @json($payment->snap_token);
            const orderUrl = @json(route('orders.show', $order->order_code));

            function pay() {
                window.snap.pay(token, {
                    onSuccess: function () { window.location.href = orderUrl + '?cek=1'; },
                    onPending: function () { window.location.href = orderUrl + '?cek=1'; },
                    onError:   function () { alert('Pembayaran gagal. Silakan coba lagi.'); },
                    onClose:   function () { /* tetap di halaman ini; tombol bisa diklik lagi */ }
                });
            }

            document.getElementById('pay_btn').addEventListener('click', pay);
            pay(); // buka popup otomatis saat halaman dimuat
        })();
    </script>
@endsection
