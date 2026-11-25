<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">

        <a href="{{ route('home') }}" class="text-2xl font-bold text-brand-600">
            Style<span class="text-gray-800">Shop</span>
        </a>

        <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
            <a href="{{ route('home') }}" class="hover:text-brand-600">Beranda</a>
            <a href="{{ route('products.index') }}" class="hover:text-brand-600">Katalog</a>
            <a href="{{ route('products.index') }}" class="hover:text-brand-600">Kategori</a>
        </nav>

        <div class="flex items-center gap-4 text-sm">
            <a href="{{ route('cart.index') }}" class="relative hover:text-brand-600">
                Keranjang
                @php($cartCount = collect(session('cart', []))->sum())
                @if ($cartCount > 0)
                    <span class="absolute -top-2 -right-4 bg-brand-600 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                @endif
            </a>
            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.products.index') }}" class="hover:text-brand-600">Admin</a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="bg-gray-100 px-4 py-2 rounded-lg">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="ml-4 bg-brand-600 text-white px-4 py-2 rounded-lg hover:bg-brand-700">Masuk</a>
            @endauth
        </div>
    </div>
</header>