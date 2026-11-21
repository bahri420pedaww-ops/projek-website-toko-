{{-- PLACEHOLDER (backend): layout publik sederhana. Boleh ditimpa desain frontend.
     Catatan: layout bawaan Breeze (x-app-layout) memakai Auth::user() sehingga
     TIDAK cocok untuk halaman publik (tamu belum login). --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Toko') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-800">
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('shop.products.index') }}" class="font-semibold text-lg">
                {{ config('app.name') }}
            </a>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('shop.products.index') }}" class="hover:underline">Produk</a>
                @auth
                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hover:underline">Admin</a>
                    @endif
                    <a href="{{ route('dashboard') }}" class="hover:underline">{{ Auth::user()->name }}</a>
                @else
                    <a href="{{ route('login') }}" class="hover:underline">Masuk</a>
                    <a href="{{ route('register') }}" class="hover:underline">Daftar</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </main>
</body>
</html>
