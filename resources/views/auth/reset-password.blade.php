<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Сброс пароля — TechnoStyle</title>
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

                        <h2 class="auth-form__title">Новый пароль</h2>

                        <form method="POST" action="{{ route('password.store') }}" class="form-auth" novalidate>
                            @csrf

                            {{-- токен сброса из ссылки --}}
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            {{-- email (он приходит в ссылке, делаем readonly чтобы не путать юзера) --}}
                            <input
                                class="form-auth__input @error('email') is-invalid @enderror"
                                type="email"
                                name="email"
                                value="{{ old('email', $request->email) }}"
                                placeholder="Email"
                                required
                                autocomplete="username"
                            >
                            @error('email')
                            <p class="invalid-feedback" style="display:block">{{ $message }}</p>
                            @enderror

                            {{-- новый пароль --}}
                            <input
                                class="form-auth__input @error('password') is-invalid @enderror"
                                type="password"
                                name="password"
                                placeholder="Новый пароль"
                                required
                                autocomplete="new-password"
                            >
                            @error('password')
                            <p class="invalid-feedback" style="display:block">{{ $message }}</p>
                            @enderror

                            {{-- подтверждение --}}
                            <input
                                class="form-auth__input @error('password_confirmation') is-invalid @enderror"
                                type="password"
                                name="password_confirmation"
                                placeholder="Подтверждение пароля"
                                required
                                autocomplete="new-password"
                            >
                            @error('password_confirmation')
                            <p class="invalid-feedback" style="display:block">{{ $message }}</p>
                            @enderror


                            <button type="submit" class="form-auth__btn">Сохранить новый пароль</button>

                            <a href="{{ route('login') }}" class="form-auth__link">Перейти к авторизации</a>
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
