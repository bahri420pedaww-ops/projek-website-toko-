<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'StyleShop')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    @include('partials.navbar')

    <main>
        @if (session('success'))
            <div class="max-w-6xl mx-auto px-4 pt-4"><p class="rounded-lg bg-green-50 p-3 text-green-700">{{ session('success') }}</p></div>
        @endif
        @if (session('error'))
            <div class="max-w-6xl mx-auto px-4 pt-4"><p class="rounded-lg bg-red-50 p-3 text-red-700">{{ session('error') }}</p></div>
        @endif
        @yield('content')
    </main>

    @include('partials.footer')

</body>
</html>