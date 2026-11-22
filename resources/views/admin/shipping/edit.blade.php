{{-- PLACEHOLDER (backend). Variabel: $method (dengan rates) --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ubah Layanan Pengiriman</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @include('admin._flash')

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.shipping-methods.update', $method) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    @include('admin.shipping._form')

                    <div class="flex items-center gap-4">
                        <x-primary-button>Perbarui</x-primary-button>
                        <a href="{{ route('admin.shipping-methods.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
