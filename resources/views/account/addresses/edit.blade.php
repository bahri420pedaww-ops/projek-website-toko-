{{-- PLACEHOLDER (backend). Variabel: $address --}}
@extends('shop.layout')

@section('title', 'Ubah Alamat')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Ubah Alamat</h1>

    <div class="bg-white p-6 rounded-lg shadow-sm max-w-2xl">
        <form method="POST" action="{{ route('addresses.update', $address->id) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('account.addresses._form')

            <div class="flex items-center gap-4">
                <button class="px-5 py-2 bg-gray-800 text-white text-sm font-semibold rounded-md hover:bg-gray-700">
                    Perbarui
                </button>
                <a href="{{ route('addresses.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
            </div>
        </form>
    </div>
@endsection
