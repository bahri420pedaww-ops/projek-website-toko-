{{-- PLACEHOLDER (backend). Variabel: $addresses (koleksi, alamat utama paling atas).
     Tiap alamat: label, receiver_name, receiver_phone, province, city, district, postal_code,
     street_address, latitude, longitude, is_default, full_address (satu baris), map_url (atau null) --}}
@extends('shop.layout')

@section('title', 'Alamat Saya')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Alamat Saya</h1>
        <a href="{{ route('addresses.create') }}"
           class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold uppercase rounded-md hover:bg-gray-700">
            Tambah Alamat
        </a>
    </div>

    @if ($addresses->isEmpty())
        <div class="bg-white p-8 rounded-lg shadow-sm text-center text-gray-600">
            Belum ada alamat tersimpan. Tambahkan alamat untuk mempercepat checkout.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($addresses as $address)
                <div class="bg-white p-5 rounded-lg shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold">{{ $address->label }}</span>
                        @if ($address->is_default)
                            <span class="text-xs font-semibold text-green-700 bg-green-50 px-2 py-0.5 rounded">Utama</span>
                        @endif
                    </div>

                    <p class="mt-2 text-sm font-medium">{{ $address->receiver_name }}</p>
                    <p class="text-sm text-gray-600">{{ $address->receiver_phone }}</p>
                    <p class="mt-1 text-sm text-gray-600">{{ $address->full_address }}</p>

                    @if ($address->map_url)
                        <a href="{{ $address->map_url }}" target="_blank" rel="noopener"
                           class="mt-1 inline-block text-xs text-indigo-600 hover:underline">Lihat di peta</a>
                    @endif

                    <div class="mt-4 flex flex-wrap items-center gap-4 text-sm">
                        <a href="{{ route('addresses.edit', $address->id) }}"
                           class="text-indigo-600 hover:underline">Ubah</a>

                        @unless ($address->is_default)
                            <form method="POST" action="{{ route('addresses.default', $address->id) }}">
                                @csrf
                                @method('PATCH')
                                <button class="text-indigo-600 hover:underline">Jadikan utama</button>
                            </form>
                        @endunless

                        <form method="POST" action="{{ route('addresses.destroy', $address->id) }}"
                              onsubmit="return confirm('Hapus alamat ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
