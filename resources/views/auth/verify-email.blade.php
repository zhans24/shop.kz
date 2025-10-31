<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Подтверждение email — TechnoStyle</title>
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

                        <h2 class="auth-form__title">Подтвердите почту</h2>

                        <p class="auth-form__desc" style="margin-bottom:1rem; font-size:14px; line-height:1.4;">
                            Мы отправили письмо с ссылкой подтверждения на ваш email.
                            Перейдите по ссылке, чтобы активировать аккаунт.
                        </p>

                        @if (session('status') == 'verification-link-sent')
                            <div class="mb-4 font-medium text-sm text-green-600">
                                На вашу почту отправлена новая ссылка подтверждения.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('verification.send') }}" class="form-auth" style="gap:16px;">
                            @csrf
                            <button type="submit" class="form-auth__btn">Отправить письмо ещё раз</button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}" class="form-auth" style="gap:16px; margin-top:16px;">
                            @csrf
                            <button type="submit" class="form-auth__btn" style="background:#777;">Выйти</button>
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
