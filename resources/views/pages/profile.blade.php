@extends('layouts.front')

@section('title','TechnoStyle')

@section('content')
    <main class="pages">
        <div class="container">
            <nav class="breadcrumbs" aria-label="Хлебные крошки">
                <ol>
                    <li><a href="{{ route('front.home') }}">Главная</a></li>
                    <li aria-current="page">Личный кабинет</li>
                </ol>
            </nav>
        </div>

        <div class="centeres">
            <section class="contacts-page">
                <span class="decor-text">Создаем комфорт</span>
            </section>

            <section class="profile">
                <div class="container">
                    <div class="profile__top">
                        <h2 class="profile__title">Личный кабинет</h2>

                        @auth
                            <form method="POST" action="{{ route('logout') }}" style="margin-left:auto;">
                                @csrf
                                <button type="submit" class="btn-hov">Выйти</button>
                            </form>
                        @endauth
                    </div>

                    <div class="profile__inner">
                        <!-- Левая колонка -->
                        <div class="profile__left">
                            <ul class="profile__list">
                                <li class="profile__item active" data-tab="profile">Профиль</li>

                                <li class="profile__item">
                                    <a href="{{ route('orders.history') }}"
                                       class="tab-link"
                                       style="color:inherit; text-decoration:none; display:block;">
                                        История заказов
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Правая колонка -->
                        <div class="profile__content">

                            @php $u = auth()->user(); @endphp

                                <!-- ===== Блок "Мои данные" ===== -->
                            <div class="profile__tab active" id="profile">
                                <h3 class="profile__content-title">Профиль</h3>

                                <form class="profile__form profile-form"
                                      method="POST"
                                      action="{{ route('profile.update') }}">
                                    @csrf
                                    @method('PATCH')

                                    {{-- Имя --}}
                                    <div class="profile-form__field">
                                        <label for="username">Имя пользователя</label>
                                        <div class="profile-form__input-wrap">
                                            <input
                                                type="text"
                                                id="username"
                                                name="name"
                                                value="{{ old('name', $u?->name) }}"
                                                readonly>
                                            <span class="profile-form__edit">Изменить</span>
                                        </div>
                                        @error('name')
                                        <div class="text-red-600 text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Телефон --}}
                                    <div class="profile-form__field">
                                        <label for="phone">Номер телефона</label>
                                        <div class="profile-form__input-wrap">
                                            <input
                                                type="text"
                                                id="phone"
                                                name="phone"
                                                value="{{ old('phone', $u?->phone ?? '') }}"
                                                readonly>
                                            <span class="profile-form__edit">Изменить</span>
                                        </div>
                                        @error('phone')
                                        <div class="text-red-600 text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div class="profile-form__field">
                                        <label for="email">Email</label>
                                        <div class="profile-form__input-wrap">
                                            <input
                                                type="email"
                                                id="email"
                                                name="email"
                                                value="{{ old('email', $u?->email) }}"
                                                readonly>
                                            <span class="profile-form__edit">Изменить</span>
                                        </div>
                                        @error('email')
                                        <div class="text-red-600 text-sm">{{ $message }}</div>
                                        @enderror

                                        {{-- верификация email --}}
                                        @if ($u instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $u->hasVerifiedEmail())
                                            <div class="text-sm text-gray-600" style="margin-top:8px; line-height:1.4;">
                                                <div style="color:#1E1512;">
                                                    Ваш email не подтверждён.
                                                </div>

                                                <form id="send-verification" method="POST" action="{{ route('verification.send') }}" style="display:inline;">
                                                    @csrf
                                                    <button type="submit"
                                                            style="background:none;border:0;padding:0;margin:0;color:#6D031A;cursor:pointer;text-decoration:underline;font-size:12px;line-height:1.4;">
                                                        Отправить письмо повторно
                                                    </button>
                                                </form>

                                                @if (session('status') === 'verification-link-sent')
                                                    <div style="color:#10b981; font-size:12px; margin-top:4px;">
                                                        Ссылка для подтверждения отправлена на ваш email.
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <div class="profile-form__footer">
                                        <button type="submit">Сохранить</button>
                                    </div>
                                </form>
                            </div>


                            <!-- ===== Блок "Смена пароля" (второй бокс) ===== -->
                            <div class="profile__tab active" style="margin-top:40px;">
                                <h3 class="profile__content-title">Смена пароля</h3>

                                <form class="profile__form profile-form" method="POST" action="{{ route('password.update') }}">
                                    @csrf
                                    @method('PUT')

                                    {{-- Поля (без локальных ошибок сбоку) --}}
                                    <div class="profile-form__field">
                                        <label for="current_password">Текущий пароль</label>
                                        <div class="profile-form__input-wrap" style="position:relative;">
                                            <input id="current_password" name="current_password" type="password"
                                                   autocomplete="current-password" placeholder="Текущий пароль">
                                            <button type="button" class="profile-form__toggle-pass" data-target="current_password"
                                                    aria-label="Показать пароль"
                                                    style="position:absolute; right:42px; top:50%; transform:translateY(-50%); background:transparent; border:0; cursor:pointer; font-size:12px;">
                                                Показать
                                            </button>
                                        </div>
                                    </div>

                                    <div class="profile-form__field">
                                        <label for="password_new">Новый пароль</label>
                                        <div class="profile-form__input-wrap" style="position:relative;">
                                            <input id="password_new" name="password" type="password"
                                                   autocomplete="new-password" placeholder="Новый пароль">
                                            <button type="button" class="profile-form__toggle-pass" data-target="password_new"
                                                    aria-label="Показать пароль"
                                                    style="position:absolute; right:42px; top:50%; transform:translateY(-50%); background:transparent; border:0; cursor:pointer; font-size:12px;">
                                                Показать
                                            </button>
                                        </div>
                                    </div>

                                    <div class="profile-form__field">
                                        <label for="password_confirmation">Подтверждение пароля</label>
                                        <div class="profile-form__input-wrap" style="position:relative;">
                                            <input id="password_confirmation" name="password_confirmation" type="password"
                                                   autocomplete="new-password" placeholder="Повторите пароль">
                                            <button type="button" class="profile-form__toggle-pass" data-target="password_confirmation"
                                                    aria-label="Показать пароль"
                                                    style="position:absolute; right:42px; top:50%; transform:translateY(-50%); background:transparent; border:0; cursor:pointer; font-size:12px;">
                                                Показать
                                            </button>
                                        </div>
                                    </div>

                                    <div class="profile-form__footer">
                                        <button type="submit">Обновить пароль</button>
                                    </div>

                                    {{-- Сводный блок ошибок именно для мешка updatePassword --}}
                                    @if ($errors->updatePassword?->any())
                                        <div class="form-errors" style="margin-top:16px; border:1px solid #FEE4E2; background:#FEF3F2; color:#B42318; border-radius:12px; padding:12px 14px;">
                                            <ul style="margin:0; padding-left:18px;">
                                                @foreach ($errors->updatePassword->all() as $msg)
                                                    <li>{{ $msg }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- Тост об успешной смене пароля --}}
                                    @if (session('status') === 'password-updated')
                                        <style>
                                            .toast-success{
                                                position:fixed;right:20px;top:20px;z-index:9999;background:#12B76A;color:#fff;
                                                padding:14px 16px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.15);
                                                font-size:14px;line-height:1.3;display:flex;gap:10px;align-items:center;
                                                opacity:0;transform:translateY(-8px);transition:opacity .25s ease, transform .25s ease;
                                            }
                                            .toast-success.show{opacity:1;transform:translateY(0)}
                                            .toast-success .toast-close{
                                                margin-left:8px;background:rgba(255,255,255,.2);border:0;color:#fff;
                                                width:28px;height:28px;border-radius:8px;cursor:pointer
                                            }
                                        </style>
                                        <div id="toastPwdSuccess" class="toast-success" role="status" aria-live="polite">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M20 7L9 18L4 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <span>Пароль обновлён.</span>
                                            <button type="button" class="toast-close" aria-label="Закрыть">×</button>
                                        </div>
                                        <script>
                                            (function () {
                                                const toast = document.getElementById('toastPwdSuccess');
                                                if (!toast) return;
                                                requestAnimationFrame(()=> toast.classList.add('show'));
                                                const closer = toast.querySelector('.toast-close');
                                                let hideTimer = setTimeout(hide, 5000);
                                                function hide(){ toast.classList.remove('show'); clearTimeout(hideTimer); }
                                                closer && closer.addEventListener('click', hide);
                                            })();
                                        </script>
                                    @endif
                                </form>
                            </div>

                        </div> <!-- /profile__content -->
                    </div> <!-- /profile__inner -->
                </div>
            </section>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // =========================
            // 1. "Изменить" / "Готово"
            // =========================
            document.addEventListener('click', (e) => {
                const editBtn = e.target.closest('.profile-form__edit');
                if (!editBtn) return;

                const wrap  = editBtn.closest('.profile-form__input-wrap');
                const input = wrap ? wrap.querySelector('input, textarea') : null;
                if (!input) return;

                const wasReadOnly = input.hasAttribute('readonly');

                if (wasReadOnly) {
                    // делаем поле редактируемым
                    input.removeAttribute('readonly');
                    editBtn.textContent = 'Готово';
                    input.focus();

                    // курсор в конец
                    const v = input.value;
                    input.value = '';
                    input.value = v;
                } else {
                    // обратно блокируем
                    input.setAttribute('readonly', 'readonly');
                    editBtn.textContent = 'Изменить';
                }

                // синхронизируем состояние кнопки "Показать"
                const toggleBtn = wrap.querySelector('.profile-form__toggle-pass');
                if (toggleBtn) {
                    const locked = input.hasAttribute('readonly');
                    toggleBtn.disabled = locked;
                    toggleBtn.style.opacity = locked ? .5 : 1;
                    toggleBtn.style.pointerEvents = locked ? 'none' : 'auto';
                }
            });

            // =========================
            // 2. Показать/скрыть пароль
            // =========================
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.profile-form__toggle-pass');
                if (!btn) return;

                const targetId = btn.getAttribute('data-target');
                if (!targetId) return;

                const input = document.getElementById(targetId);
                if (!input) return;

                const isPass = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPass ? 'text' : 'password');
                btn.textContent = isPass ? 'Скрыть' : 'Показать';
            });

        });
    </script>
@endsection
