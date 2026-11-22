{{-- PLACEHOLDER (backend). Variabel: $customer (User), $addresses (koleksi, alamat utama paling atas).
     Tiap alamat: label, receiver_name, receiver_phone, full_address, map_url (atau null), is_default --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Pelanggan</h2>
            <a href="{{ route('admin.customers.index') }}" class="text-sm text-gray-600 hover:underline">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="font-semibold text-gray-800 mb-3">Data Akun</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                    <div>
                        <dt class="text-gray-500">Nama</dt>
                        <dd class="font-medium">{{ $customer->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Email</dt>
                        <dd class="font-medium">{{ $customer->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">No. HP</dt>
                        <dd class="font-medium">{{ $customer->phone ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Terdaftar</dt>
                        <dd class="font-medium">{{ $customer->created_at?->format('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="font-semibold text-gray-800 mb-3">Alamat ({{ $addresses->count() }})</h3>

                @if ($addresses->isEmpty())
                    <p class="text-sm text-gray-500">Pelanggan ini belum menyimpan alamat.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($addresses as $address)
                            <div class="border rounded-md p-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold">{{ $address->label }}</span>
                                    @if ($address->is_default)
                                        <span class="text-xs font-semibold text-green-700 bg-green-50 px-2 py-0.5 rounded">Utama</span>
                                    @endif
                                </div>

                                <p class="mt-2 font-medium">{{ $address->receiver_name }}</p>
                                <p class="text-gray-600">{{ $address->receiver_phone }}</p>
                                <p class="mt-1 text-gray-600">{{ $address->full_address }}</p>

                                @if ($address->map_url)
                                    <a href="{{ $address->map_url }}" target="_blank" rel="noopener"
                                       class="mt-2 inline-block text-xs text-indigo-600 hover:underline">
                                        Lihat di peta
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
