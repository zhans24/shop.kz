<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Авторизация — TechnoStyle</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.svg') }}">

    @php
        function vasset($path) {
            $full = public_path($path);
            return asset($path).(file_exists($full)?'?v='.filemtime($full):'');
        }
    @endphp

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

                        <h2 class="auth-form__title">Авторизация</h2>

                        {{-- статус (например "мы отправили ссылку на сброс") --}}
                        @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-600">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="form-auth" novalidate>
                            @csrf

                            {{-- Email --}}
                            <input
                                class="form-auth__input @error('email') is-invalid @enderror"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Email"
                                required
                                autocomplete="username"
                            >
                            @error('email')
                            <p class="invalid-feedback" style="display:block">{{ $message }}</p>
                            @enderror

                            {{-- Пароль --}}
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

                            {{-- Запомнить меня --}}
                            <label class="form-auth__checkbox">
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span class="form-auth__checkmark"></span>
                                <span class="checkbox-text">Запомнить меня</span>
                            </label>

                            <button type="submit" class="form-auth__btn">Войти</button>

                            <a href="{{ route('password.request') }}" class="form-auth__link">Забыли пароль?</a>

                            <div class="form-auth__link" style="margin-top: 8px;">
                                Нет аккаунта?
                                <a href="{{ route('register') }}" style="text-decoration:underline;">Зарегистрируйтесь</a>
                            </div>
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
