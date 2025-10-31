<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Регистрация — TechnoStyle</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.svg') }}">

    @php
        function vasset($path) {
            $full = public_path($path);
            return asset($path).(file_exists($full)?'?v='.filemtime($full):'');
        }
    @endphp

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.3/build/css/intlTelInput.min.css">
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
                        <h2 class="auth-form__title">Регистрация</h2>

                        {{-- статус (если нужно показать что-то типа "ссылка подтверждения отправлена") --}}
                        @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-600">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" class="form-auth" novalidate>
                            @csrf

                            {{-- Имя --}}
                            <input
                                class="form-auth__input @error('name') is-invalid @enderror"
                                type="text"
                                name="name"
                                placeholder="Имя"
                                value="{{ old('name') }}"
                                required
                                autocomplete="name"
                            >
                            @error('name')
                            <p class="invalid-feedback" style="display:block">{{ $message }}</p>
                            @enderror

                            {{-- Email --}}
                            <input
                                class="form-auth__input @error('email') is-invalid @enderror"
                                type="email"
                                name="email"
                                placeholder="Email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="username"
                            >
                            @error('email')
                            <p class="invalid-feedback" style="display:block">{{ $message }}</p>
                            @enderror

                            {{-- Телефон --}}
                            <input
                                class="form-auth__input @error('phone') is-invalid @enderror"
                                id="phone"
                                type="tel"
                                name="phone"
                                placeholder="Номер телефона"
                                value="{{ old('phone') }}"
                            >
                            @error('phone')
                            <p class="invalid-feedback" style="display:block">{{ $message }}</p>
                            @enderror

                            {{-- Пароль --}}
                            <input
                                class="form-auth__input @error('password') is-invalid @enderror"
                                type="password"
                                name="password"
                                placeholder="Пароль"
                                required
                                autocomplete="new-password"
                            >
                            @error('password')
                            <p class="invalid-feedback" style="display:block">{{ $message }}</p>
                            @enderror

                            {{-- Подтверждение --}}
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

                            {{-- Согласие (если делаешь required в контроллере -> accepted) --}}
                            <label class="form-auth__checkbox">
                                <input type="checkbox" name="agree" value="1" {{ old('agree') ? 'checked' : '' }}>
                                <span class="form-auth__checkmark"></span>
                                <a href="{{ route('front.privacy') }}" class="checkbox-text">
                                    Согласие с конфиденциальности / условиями использовании
                                </a>
                            </label>
                            @error('agree')
                            <p class="invalid-feedback" style="display:block">{{ $message }}</p>
                            @enderror

                            <button type="submit" class="form-auth__btn">Регистрация</button>

                            <a href="{{ route('login') }}" class="form-auth__link">уже есть аккаунт?</a>
                        </form>
                    </div>
                </div>
            </section>

        </div>
    </main>

    @include('partials.footer')
</div>



<script src="{{ vasset('js/index.js') }}"></script>
<script src="{{ vasset('js/cart.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.3/build/js/intlTelInput.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.querySelector("#phone");
        if (!input) return;

        const iti = window.intlTelInput(input, {
            initialCountry: "kz",
            nationalMode: false,
            autoHideDialCode: false,
            formatOnDisplay: true,
            separateDialCode: false,
            showSelectedDialCode: true,
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.3/build/js/utils.js",
        });


        input.addEventListener('input', function () {
            const cleaned = this.value.replace(/[^\d+]/g, '');
            if (this.value !== cleaned) {
                this.value = cleaned;
            }

            const maxLength = 10;
            if (this.value.length > maxLength) {
                this.value = this.value.slice(0, maxLength);
            }
        });
        input.addEventListener('blur', function () {
            const val = input.value.trim();
            if (val === '') {
                this.value = '+' + iti.getSelectedCountryData().dialCode;
            }
        });

        const form = input.closest("form");
        if (form) {
            form.addEventListener("submit", function () {
                input.value = iti.getNumber();
            });
        }
    });

</script>

</body>
</html>
