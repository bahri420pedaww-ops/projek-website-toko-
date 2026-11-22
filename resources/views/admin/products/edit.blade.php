{{-- PLACEHOLDER (backend). Variabel: $product (dengan images), $categories --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ubah Produk</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('admin._flash')

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.products.update', $product) }}"
                      enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    @include('admin.products._form')

                    <div class="flex items-center gap-4">
                        <x-primary-button>Perbarui</x-primary-button>
                        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:underline">Kembali</a>
                    </div>
                </form>
            </div>

            {{-- Pengelolaan gambar: sengaja di luar form utama (form tidak boleh bersarang) --}}
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="font-semibold text-gray-800 mb-4">Gambar Produk</h3>

                @if ($product->images->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada gambar.</p>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach ($product->images as $image)
                            <div class="border rounded-md p-2 text-sm">
                                <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $product->name }}"
                                     class="h-32 w-full object-cover rounded">

                                <div class="mt-2 flex items-center justify-between">
                                    @if ($image->is_primary)
                                        <span class="text-xs font-semibold text-green-700">Gambar utama</span>
                                    @else
                                        <form method="POST"
                                              action="{{ route('admin.products.images.primary', [$product, $image]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="text-indigo-600 hover:underline">Jadikan utama</button>
                                        </form>
                                    @endif

                                    <form method="POST"
                                          action="{{ route('admin.products.images.destroy', [$product, $image]) }}"
                                          onsubmit="return confirm('Hapus gambar ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
