<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','TechnoStyle')</title>
    @hasSection('meta_description')
        <meta name="description" content="@yield('meta_description')">
    @endif

    @php
        function vasset($path) {
            $full = public_path($path);
            return asset($path) . (file_exists($full) ? ('?v='.filemtime($full)) : '');
        }
    @endphp

    {{-- как в верстке --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="{{ vasset('css/main.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/styles.css') }}">
</head>
<body>
<div class="wrapper">
    @include('partials.header')
    <main>@yield('content')</main>
    @include('partials.footer')
</div>

{{-- порядок как надо: jQuery -> плагины -> Swiper -> твои скрипты --}}
<script src="{{ vasset('js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ vasset('js/jquery.maskedinput.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
<script src="{{ vasset('js/index.js') }}"></script>
<script src="{{ vasset('js/cart.js') }}"></script>
</body>
</html>
