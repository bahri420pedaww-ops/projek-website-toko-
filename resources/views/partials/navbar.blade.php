<header class="sticky top-0 z-50 bg-white shadow-sm">
    <div class="mx-auto max-w-6xl px-4">
        <div class="flex h-16 items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="shrink-0 text-2xl font-bold text-brand-600">
                Style<span class="text-gray-800">Shop</span>
            </a>

            <div class="hidden min-w-0 flex-1 items-center justify-center gap-5 md:flex">
                <nav class="flex shrink-0 items-center gap-4 text-sm font-medium lg:gap-6">
                    <a href="{{ route('home') }}" class="whitespace-nowrap transition hover:text-brand-600">Beranda</a>
                    <a href="{{ route('products.index') }}" class="whitespace-nowrap transition hover:text-brand-600">Katalog</a>
                    <a href="{{ route('home') }}#kategori" class="whitespace-nowrap transition hover:text-brand-600">Kategori</a>
                </nav>

                <form action="{{ route('products.index') }}" method="GET" role="search" class="relative min-w-0">
                    <label for="navbar-search-desktop" class="sr-only">Cari produk</label>
                    <input id="navbar-search-desktop" type="search" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                           class="w-40 rounded-full border border-gray-200 bg-gray-50 py-2 pl-4 pr-10 text-sm outline-none transition focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-100 lg:w-56">
                    <button type="submit" aria-label="Cari" class="absolute inset-y-0 right-0 inline-flex w-10 items-center justify-center text-gray-500 transition hover:text-brand-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" />
                            <path stroke-linecap="round" d="m20 20-4-4" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="flex shrink-0 items-center gap-3 text-sm sm:gap-4">
                @auth
                    @if (auth()->user()->role === 'customer')
                        <a href="{{ route('orders.index') }}" aria-label="Pesanan Saya" title="Pesanan Saya" class="relative inline-flex items-center gap-1 whitespace-nowrap font-medium transition hover:text-brand-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16v15H4zM8 3v4m8-4v4M8 11h8m-8 4h5" />
                            </svg>
                            <span class="hidden sm:inline">Pesanan Saya</span>
                            <span class="sm:hidden">Pesanan</span>
                            @if ($orderCount > 0)
                                <span class="absolute -right-3 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold leading-none text-white" aria-label="{{ $orderCount }} pesanan">
                                    {{ $orderCount }}
                                </span>
                            @endif
                        </a>
                    @endif
                @endauth
                @if (!auth()->check() || auth()->user()->role === 'customer')
                    <a href="{{ route('cart.index') }}" class="relative inline-flex items-center font-medium transition hover:text-brand-600">
                        Keranjang
                        @if ($cartCount > 0)
                            <span class="absolute -right-3 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold leading-none text-white" aria-label="{{ $cartCount }} barang di keranjang">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                @endif

                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.products.index') }}" class="hidden whitespace-nowrap transition hover:text-brand-600 sm:inline">Admin</a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="whitespace-nowrap font-medium transition hover:text-brand-600">Profil</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="whitespace-nowrap rounded-lg bg-gray-100 px-3 py-2 font-medium transition hover:bg-gray-200 sm:px-4">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="whitespace-nowrap rounded-lg bg-brand-600 px-3 py-2 font-medium text-white transition hover:bg-brand-700 sm:px-4">Masuk</a>
                @endauth
            </div>
        </div>

        <form action="{{ route('products.index') }}" method="GET" role="search" class="relative pb-3 md:hidden">
            <label for="navbar-search-mobile" class="sr-only">Cari produk</label>
            <input id="navbar-search-mobile" type="search" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                   class="w-full rounded-full border border-gray-200 bg-gray-50 py-2.5 pl-4 pr-11 text-sm outline-none transition focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-100">
            <button type="submit" aria-label="Cari" class="absolute bottom-3 right-0 inline-flex h-9 w-11 items-center justify-center text-gray-500 transition hover:text-brand-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" d="m20 20-4-4" />
                </svg>
            </button>
        </form>
    </div>
</header>
