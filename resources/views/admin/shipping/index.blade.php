{{-- PLACEHOLDER (backend). Variabel: $methods (dengan rates) --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengiriman dan Tarif Ongkir</h2>
            <a href="{{ route('admin.shipping-methods.create') }}"
               class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold uppercase rounded-md hover:bg-gray-700">
                Tambah Layanan
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('admin._flash')

            <div class="mb-4 rounded-md bg-gray-50 border p-4 text-sm text-gray-600">
                Lokasi toko (asal pengiriman):
                <span class="font-medium">{{ config('shipping.origin_city') }}, {{ config('shipping.origin_province') }}</span>.
                Ubah lewat <code>SHOP_ORIGIN_CITY</code> dan <code>SHOP_ORIGIN_PROVINCE</code> di file <code>.env</code>.
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Layanan</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Zona 1<br><span class="text-xs font-normal">satu kota</span></th>
                            <th class="px-4 py-3">Zona 2<br><span class="text-xs font-normal">satu provinsi</span></th>
                            <th class="px-4 py-3">Zona 3<br><span class="text-xs font-normal">satu pulau</span></th>
                            <th class="px-4 py-3">Zona 4<br><span class="text-xs font-normal">beda pulau</span></th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($methods as $method)
                            @php $rates = $method->rates->keyBy('zone'); @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $method->courier }} {{ $method->service }}</td>
                                <td class="px-4 py-3">{{ $method->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                                @foreach ([1, 2, 3, 4] as $zone)
                                    <td class="px-4 py-3">
                                        @if ($rates->has($zone))
                                            Rp {{ number_format($rates[$zone]->price_per_kg, 0, ',', '.') }}/kg
                                            @if ($rates[$zone]->estimated_days)
                                                <br><span class="text-xs text-gray-500">{{ $rates[$zone]->estimated_days }}</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">tidak melayani</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.shipping-methods.edit', $method) }}"
                                       class="text-indigo-600 hover:underline">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada layanan pengiriman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
