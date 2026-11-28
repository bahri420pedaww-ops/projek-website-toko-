{{-- PLACEHOLDER (backend). Variabel: $toProcess (sudah dibayar, perlu diproses), $toShip (sedang diproses,
     perlu dikirim), $unpaid (menunggu pembayaran), $shipped (sedang dikirim) --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Admin</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Ringkasan pesanan --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('admin.orders.index', ['status' => 'paid']) }}"
                   class="bg-white p-5 shadow-sm sm:rounded-lg hover:bg-gray-50">
                    <p class="text-xs text-gray-500">Perlu diproses</p>
                    <p class="mt-1 text-2xl font-bold text-blue-700">{{ $toProcess }}</p>
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}"
                   class="bg-white p-5 shadow-sm sm:rounded-lg hover:bg-gray-50">
                    <p class="text-xs text-gray-500">Perlu dikirim</p>
                    <p class="mt-1 text-2xl font-bold text-indigo-700">{{ $toShip }}</p>
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}"
                   class="bg-white p-5 shadow-sm sm:rounded-lg hover:bg-gray-50">
                    <p class="text-xs text-gray-500">Sedang dikirim</p>
                    <p class="mt-1 text-2xl font-bold text-purple-700">{{ $shipped }}</p>
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
                   class="bg-white p-5 shadow-sm sm:rounded-lg hover:bg-gray-50">
                    <p class="text-xs text-gray-500">Menunggu pembayaran</p>
                    <p class="mt-1 text-2xl font-bold text-yellow-700">{{ $unpaid }}</p>
                </a>
            </div>

            {{-- Menu --}}
            <div class="space-y-4">
                <a href="{{ route('admin.orders.index') }}"
                   class="block bg-white p-6 shadow-sm sm:rounded-lg text-gray-800 hover:bg-gray-50">
                    Kelola Pesanan
                </a>
                <a href="{{ route('admin.reports.index') }}"
                   class="block bg-white p-6 shadow-sm sm:rounded-lg text-gray-800 hover:bg-gray-50">
                    Laporan Penjualan
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="block bg-white p-6 shadow-sm sm:rounded-lg text-gray-800 hover:bg-gray-50">
                    Kelola Kategori
                </a>
                <a href="{{ route('admin.products.index') }}"
                   class="block bg-white p-6 shadow-sm sm:rounded-lg text-gray-800 hover:bg-gray-50">
                    Kelola Produk
                </a>
                <a href="{{ route('admin.customers.index') }}"
                   class="block bg-white p-6 shadow-sm sm:rounded-lg text-gray-800 hover:bg-gray-50">
                    Pelanggan dan Alamat
                </a>
                <a href="{{ route('admin.shipping-methods.index') }}"
                   class="block bg-white p-6 shadow-sm sm:rounded-lg text-gray-800 hover:bg-gray-50">
                    Pengiriman dan Tarif Ongkir
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
