<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Подтвердите пароль — TechnoStyle</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.svg') }}">

    @php
        function vasset($path) {
            $full = public_path($path);
            return asset($path).(file_exists($full)?'?v='.filemtime($full):'');
        }
    @endphp

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="{{ vasset('css/main.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/styles.css') }}">
</head>
<body>
<div class="wrapper auth">

    @include('partials.header', [
        'headerTheme' => 'dark',
        'headerModifier' => 'pages-header__sticky',
    ])

    <main class="pages">
        <div class="centeres">
            <section class="contacts-page">
                <span class="decor-text">Создаем комфорт</span>
            </section>

            <section class="auth-form">
                <div class="container">
                    <div class="auth-form__inner">
                        <h2 class="auth-form__title">Подтвердите пароль</h2>

                        <p class="auth-form__desc" style="margin-bottom:1rem; font-size:14px; line-height:1.4;">
                            Это защищенная зона. Введите пароль ещё раз, чтобы продолжить.
                        </p>

                        <form method="POST" action="{{ route('password.confirm') }}" class="form-auth" novalidate>
                            @csrf

                            <input
                                class="form-auth__input @error('password') is-invalid @enderror"
                                type="password"
                                name="password"
                                placeholder="Пароль"
                                required
                                autocomplete="current-password"
                            >
                            @error('password')
                            <p class="invalid-feedback" style="display:block">{{ $message }}</p>
                            @enderror

                            <button type="submit" class="form-auth__btn">Подтвердить</button>
                        </form>

                    </div>
                </div>
            </section>
        </div>
    </main>

    @include('partials.footer')
</div>

</body>
</html>
