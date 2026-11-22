{{-- PLACEHOLDER (backend). Variabel: $customers (paginator), $keyword.
     Tiap pelanggan: name, email, phone, created_at, addresses_count --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pelanggan</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('admin._flash')

            <form method="GET" class="mb-4 flex flex-wrap gap-2">
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari nama, email, atau nomor HP"
                       class="border-gray-300 rounded-md shadow-sm text-sm w-72">
                <button class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold uppercase rounded-md">Cari</button>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">No. HP</th>
                            <th class="px-4 py-3">Alamat</th>
                            <th class="px-4 py-3">Terdaftar</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="px-4 py-3">{{ $customer->name }}</td>
                                <td class="px-4 py-3">{{ $customer->email }}</td>
                                <td class="px-4 py-3">{{ $customer->phone ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $customer->addresses_count }}</td>
                                <td class="px-4 py-3">{{ $customer->created_at?->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.customers.show', $customer) }}"
                                       class="text-indigo-600 hover:underline">Lihat</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                    Belum ada pelanggan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $customers->links() }}</div>
        </div>
    </div>
</x-app-layout>
