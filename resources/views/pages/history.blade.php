@php use Illuminate\Support\Carbon; @endphp
@extends('layouts.front')

@section('title','История заказов')

@section('content')
    <main class="pages">
        <div class="container">
            <nav class="breadcrumbs" aria-label="Хлебные крошки">
                <ol>
                    <li><a href="{{ route('front.home') }}">Главная</a></li>
                    <li><a href="{{ route('profile.edit') }}">Личный кабинет</a></li>
                    <li aria-current="page">История заказов</li>
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
                        {{-- На странице истории кнопку "Выйти" НЕ показываем (по твоему правилу) --}}
                    </div>

                    <div class="profile__inner">
                        <!-- Левая колонка (та же, что в профиле) -->
                        <div class="profile__left">
                            <ul class="profile__list">
                                <li class="profile__item">
                                    <a href="{{ route('profile.edit') }}"
                                       class="tab-link"
                                       style="color:inherit; text-decoration:none; display:block;">
                                        Профиль
                                    </a>
                                </li>
                                <li class="profile__item active" data-tab="orders">
                                    <a href="{{ route('orders.history') }}"
                                       class="tab-link"
                                       style="color:inherit; text-decoration:none; display:block;">
                                        История заказов
                                    </a>
                                </li>
                                {{-- Баланс убран --}}
                            </ul>
                        </div>

                        <!-- Правая часть -->
                        <div class="profile__content">
                            <!-- ===== История заказов ===== -->
                            <div class="profile__tab active" id="orders">
                                <h3 class="profile__content-title">История заказов</h3>

                                <div class="orders-list">
                                    @forelse($items as $item)
                                        <a href="#!" class="order-item">
                                            <div class="order-image-wrap">
                                                {{-- если у item есть картинка, подставь её, иначе плейсхолдер --}}
                                                <img src="{{ $item->image_url ?? asset('img/prod/g1.png') }}" alt="">
                                            </div>
                                            <div class="order-item__info">
                                                <p>{{ $item->name }}</p>
                                                <p>
                                                <span class="new-price">
                                                    {{ number_format($item->price, 0, ',', ' ') }} Т
                                                </span>
                                                </p>
                                                <p>{{ $item->qty }} шт</p>
                                                <p class="order-item__date">
                                                    {{ Carbon::parse($item->ordered_at)->translatedFormat('d F Y, H:i') }}                                                </p>
                                            </div>
                                        </a>
                                    @empty
                                        <p>У вас пока нет заказов.</p>
                                    @endforelse

                                    {{-- Пагинация (стрелками, как в макете) --}}
                                    @if($items->hasPages())
                                        <ul class="order-pagination">
                                            {{-- prev --}}
                                            @if($items->onFirstPage())
                                                <li><span aria-disabled="true">&lt;</span></li>
                                            @else
                                                <li><a href="{{ $items->previousPageUrl() }}">&lt;</a></li>
                                            @endif

                                            {{-- текущую страницу можно подсветить, но cursorPaginate без номеров.
                                                 Оставляем только стрелки, чтобы не ломать стили. --}}

                                            {{-- next --}}
                                            @if($items->hasMorePages())
                                                <li><a href="{{ $items->nextPageUrl() }}">&gt;</a></li>
                                            @else
                                                <li><span aria-disabled="true">&gt;</span></li>
                                            @endif
                                        </ul>
                                    @endif
                                </div>
                            </div>
                            {{-- других вкладок не выводим здесь --}}
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    @if (session('ok'))
        <style>
            .toast-success {
                position: fixed;
                right: 20px;
                top: 20px;
                z-index: 9999;
                background: #12B76A;
                color: #fff;
                padding: 14px 16px;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, .15);
                font-size: 14px;
                line-height: 1.3;
                display: flex;
                gap: 10px;
                align-items: center;
                opacity: 0;
                transform: translateY(-8px);
                transition: opacity .25s ease, transform .25s ease;
            }

            .toast-success.show {
                opacity: 1;
                transform: translateY(0);
            }

            .toast-success .toast-close {
                margin-left: 8px;
                background: rgba(255, 255, 255, .2);
                border: 0;
                color: #fff;
                width: 28px;
                height: 28px;
                border-radius: 8px;
                cursor: pointer;
            }
        </style>

        <div id="toastSuccess" class="toast-success" role="status" aria-live="polite">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M20 7L9 18L4 13" stroke="white" stroke-width="2" stroke-linecap="round"
                      stroke-linejoin="round"/>
            </svg>
            <span>{{ session('ok') }}</span>
            <button type="button" class="toast-close" aria-label="Закрыть">×</button>
        </div>

        <script>
            (function () {
                const CART_KEY = 'cartItems';

                // 1) очищаем корзину
                try {
                    localStorage.removeItem(CART_KEY);
                } catch (e) {
                }

                // 2) обновляем бейдж на иконке корзины
                (function updateCartCount() {
                    const basketLink = document.querySelector(".basket-link");
                    if (!basketLink) return;
                    let badge = basketLink.querySelector(".cart-count");
                    if (!badge) {
                        badge = document.createElement("span");
                        badge.classList.add("cart-count");
                        badge.style.cssText = `
                        position:absolute; top:-5px; right:-10px;
                        background:red; color:white; font-size:11px;
                        padding:2px 6px; border-radius:10px;
                    `;
                        basketLink.style.position = "relative";
                        basketLink.appendChild(badge);
                    }
                    badge.textContent = '0';
                })();

                // 3) показываем тост
                const toast = document.getElementById('toastSuccess');
                if (toast) {
                    requestAnimationFrame(() => toast.classList.add('show'));
                    const closer = toast.querySelector('.toast-close');
                    let hideTimer = setTimeout(hide, 5000);
                    closer?.addEventListener('click', hide);

                    function hide() {
                        toast.classList.remove('show');
                        clearTimeout(hideTimer);
                    }
                }
            })();
        </script>
    @endif

@endsection
