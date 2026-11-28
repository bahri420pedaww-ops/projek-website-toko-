{{-- Tata letak halaman error. SENGAJA mandiri (tanpa database, login, atau Vite) supaya tetap
     tampil walau penyebab errornya ada di bagian itu. --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') - {{ config('shop.name', config('app.name')) }}</title>
    <style>
        body { margin: 0; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
               background: #f3f4f6; color: #1f2937; display: flex; align-items: center;
               justify-content: center; min-height: 100vh; }
        .box { background: #fff; padding: 2.5rem; border-radius: .75rem; max-width: 28rem; width: calc(100% - 2rem);
               text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
        .code { font-size: 3.5rem; font-weight: 800; color: #9ca3af; margin: 0; line-height: 1; }
        h1 { font-size: 1.25rem; margin: .75rem 0 .5rem; }
        p { color: #6b7280; margin: 0 0 1.5rem; line-height: 1.5; }
        a { display: inline-block; background: #1f2937; color: #fff; text-decoration: none;
            padding: .6rem 1.2rem; border-radius: .5rem; font-size: .9rem; }
        a:hover { background: #374151; }
    </style>
</head>
<body>
    <div class="box">
        <p class="code">@yield('code')</p>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <a href="{{ url('/produk') }}">Kembali ke toko</a>
    </div>
</body>
</html>
