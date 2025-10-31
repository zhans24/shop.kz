@extends('layouts.front')

@section('title', $title)
@if($desc) @section('meta_description', $desc) @endif

@section('content')
    <main class="pages">
        <div class="container">
            <nav class="breadcrumbs" aria-label="Хлебные крошки">
                <ol>
                    <li><a href="{{ route('front.home') }}">Главная</a></li>
                    <li><a href="{{ route('categories.index') }}">Категории</a></li>
                    <li aria-current="page">{{ $h1 }}</li>
                </ol>
            </nav>
        </div>

        <div class="centeres">
            <section class="products-page">
                <span class="decor-text">Создаем комфорт</span>

                <section class="products">
                    <div class="container">
                        <div class="products__top">
                            <h2>{{ $h1 }}</h2>

                            {{-- Сортировка (GET) --}}
                            <form method="GET" id="sortForm">
                                @foreach(request()->except('sort','page') as $k => $v)
                                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                @endforeach

                                @php
                                    $sortMap = [
                                        ''            => 'По популярности',
                                        'popular'     => 'По популярности',
                                        'price_asc'   => 'По возрастанию цены',
                                        'price_desc'  => 'По убыванию цены',
                                    ];
                                    $currentSort = $sortMap[$sort ?? 'popular'] ?? 'По популярности';
                                @endphp

                                <div class="custom-select">
                                    <div class="select-selected">
                                        <span class="selected-span">{{ $currentSort }}</span>
                                        <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.75 0.75L6.75 6.75L12.75 0.75" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <ul class="select-options">
                                        <li data-value="popular">По популярности</li>
                                        <li data-value="price_asc">По возрастанию цены</li>
                                        <li data-value="price_desc">По убыванию цены</li>
                                    </ul>
                                    <input type="hidden" name="sort" value="{{ $sort ?: 'popular' }}">
                                </div>
                            </form>
                        </div>

                        {{-- Фильтр (GET) с выпадающими списками --}}
                        @php
                            $limit = (int)($priceMaxLimit ?? 345000);
                            $min = (int)($filters['price_min'] ?? 0);
                            $max = (int)($filters['price_max'] ?? $limit);
                            $min = max(0, $min);
                            $max = max($min, min($max, $limit));
                        @endphp

                        {{-- Фильтр (оставляю твою вёрстку) --}}
                        <form class="filter" method="GET" id="filterForm">
                            <h3 class="filter-title">Фильтр</h3>
                            <div class="filter-row">
                                {{-- Производитель --}}
                                <select name="brand_id" id="brandText">
                                    <option value="">Производитель</option>
                                    @foreach($brands as $b)
                                        <option value="{{ $b->id }}" @selected((int)($filters['brand_id'] ?? 0) === $b->id)>
                                            {{ $b->name }}
                                        </option>
                                    @endforeach
                                </select>

                                {{-- Тип --}}
                                <select name="type" id="typeText">
                                    <option value="">Тип</option>
                                    @foreach($types as $t)
                                        @php $optVal = $t->slug ?: $t->value; @endphp
                                        <option value="{{ $optVal }}" @selected(($filters['type'] ?? '') === $optVal)>
                                            {{ $t->value }}
                                        </option>
                                    @endforeach
                                </select>

                                {{-- Цена --}}
                                @php
                                    $limit = (int)($priceMaxLimit ?? 345000);
                                    $min = max(0, (int)($filters['price_min'] ?? 0));
                                    $max = (int)($filters['price_max'] ?? $limit);
                                    if ($max < $min) $max = $min;
                                @endphp
                                <div class="price-range">
                                    <label>Цена</label>
                                    <div class="inputs">
                                        <input type="number" id="minPrice" name="price_min" value="{{ $min }}" min="0" max="{{ $limit }}" step="1000">
                                        <input type="number" id="maxPrice" name="price_max" value="{{ $max }}" min="0" max="{{ $limit }}" step="1000">
                                    </div>
                                    <div class="slider">
                                        <div class="progress" style="left: {{ ($min/$limit)*100 }}%; right: {{ 100 - ($max/$limit)*100 }}%;"></div>
                                        <input type="range" id="rangeMin" min="0" max="{{ $limit }}" value="{{ $min }}" step="1000">
                                        <input type="range" id="rangeMax" min="0" max="{{ $limit }}" value="{{ $max }}" step="1000">
                                    </div>
                                </div>

                                <button class="filter-btn" type="submit">Поиск</button>

                                <a class="filter-reset-link" href="{{ route('category.show', $category->slug) }}" style="margin-left:12px">                                    Сбросить
                                </a>

                            </div>

                            {{-- сохраняем текущую сортировку --}}
                            <input type="hidden" name="sort" value="{{ $sort ?: 'popular' }}">
                        </form>

                        {{-- Сетка товаров --}}
                        <div class="product-grid">
                            @forelse($products as $p)
                                @php
                                    $img = $p->coverUrl('thumb');
                                    $isDiscount = $p->hasActiveDiscount() && $p->discount_percent;
                                    $priceText = number_format($p->price, 0, '.', ' ') . ' ₸';
                                @endphp

                                <a href="{{ route('product.show', $p->slug) }}"
                                   class="product-card {{ $isDiscount ? 'discount' : '' }}"
                                   id="p-{{ $p->id }}">
                                    @if($isDiscount)
                                        <span class="badge">{{ (int)$p->discount_percent }}%</span>
                                    @endif

                                    <div class="product-card-top">
                                        <img src="{{ $img ?? '/img/no-image.webp' }}" alt="{{ $p->name }}" loading="lazy" decoding="async" />
                                    </div>

                                    <div class="prod-bottom">
                                        <h3>{{ $p->name }}</h3>
                                        <p class="price">{{ $priceText }}</p>
                                    </div>
                                </a>
                            @empty
                                <p>Ничего не найдено.</p>
                            @endforelse
                        </div>

                        {{-- Пагинация --}}
                        @if($products->hasPages())
                            <ul class="paginations">
                                <li>
                                    @if($products->onFirstPage())
                                        <span class="page-btn prev disabled">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.06 12L11 11.06L7.94667 8L11 4.94L10.06 4L6.06 8L10.06 12Z" fill="black"/></svg>
                                    </span>
                                    @else
                                        <a href="{{ $products->previousPageUrl() }}" class="page-btn prev">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.06 12L11 11.06L7.94667 8L11 4.94L10.06 4L6.06 8L10.06 12Z" fill="black"/></svg>
                                        </a>
                                    @endif
                                </li>

                                @for ($page = 1; $page <= $products->lastPage(); $page++)
                                    @php $url = $products->url($page); $isCurrent = $page === $products->currentPage(); @endphp
                                    <li>
                                        @if ($isCurrent)
                                            <span class="page-btn active">{{ $page }}</span>
                                        @else
                                            <a class="page-btn" href="{{ $url }}">{{ $page }}</a>
                                        @endif
                                    </li>
                                @endfor

                                <li>
                                    @if($products->hasMorePages())
                                        <a href="{{ $products->nextPageUrl() }}" class="page-btn next">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.94 4L6 4.94L9.05333 8L6 11.06L6.94 12L10.94 8L6.94 4Z" fill="black"/></svg>
                                        </a>
                                    @else
                                        <span class="page-btn next disabled">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.94 4L6 4.94L9.05333 8L6 11.06L6.94 12L10.94 8L6.94 4Z" fill="black"/></svg>
                                    </span>
                                    @endif
                                </li>
                            </ul>
                        @endif
                    </div>
                </section>
            </section>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('checkoutForm');
            if (!form) return;

            // 1) Помечаем обязательные поля БЕЗ изменения разметки
            const mustHave = ['contact_name', 'phone', 'address'];
            mustHave.forEach((name) => {
                const el = form.querySelector(`[name="${name}"]`);
                if (el) el.required = true;
            });

            // Телефон: валидируем формат, но только через JS (верстку не трогаем)
            const phone = form.querySelector('[name="phone"]');
            if (phone) {
                phone.setAttribute('pattern', '^\\+?7[\\s\\-]?\\d{3}[\\s\\-]?\\d{3}[\\s\\-]?\\d{2}[\\s\\-]?\\d{2}$');
                phone.setAttribute('title', 'Введите телефон в формате +7 747 123 45 67');
                // сбрасываем кастомные сообщения при вводе
                phone.addEventListener('input', () => phone.setCustomValidity(''));
            }

            // 2) Радиогруппы: required ставим на ПЕРВУЮ кнопку каждой группы
            function requireRadioGroup(name) {
                const radios = form.querySelectorAll(`input[type="radio"][name="${name}"]`);
                if (radios.length) radios[0].required = true;
            }
            ['delivery_method_id', 'customer_type', 'payment_method_id'].forEach(requireRadioGroup);

            // 3) Сайдбар-кнопка: включаем нативную HTML5-валидацию (пузыри у полей)
            const asideBtn = document.getElementById('asideSubmit');
            if (asideBtn) {
                asideBtn.addEventListener('click', (e) => {
                    // пересчитать итоги перед проверкой, если у тебя есть recalc()
                    if (typeof recalc === 'function') recalc();

                    // reportValidity() покажет подсказку прямо у проблемного поля
                    if (form.reportValidity()) {
                        // ВАЖНО: requestSubmit() — запускает стандартную валидацию и события submit
                        form.requestSubmit();
                    }
                });
            }

            // 4) Если где-то раньше попал novalidate — уберём, чтобы браузер валидировал сам
            form.removeAttribute('novalidate');

            // 5) На всякий случай: при сабмите через кнопку внутри формы браузер и так валидирует
            // Ничего не меняем — только аккуратно ловим invalid для красивых сообщений
            form.addEventListener('invalid', (ev) => {
                const el = ev.target;
                // кастомные короткие тексты, не меняя верстку
                const map = {
                    contact_name: 'Введите ваше имя',
                    phone: 'Введите телефон в формате +7 747 123 45 67',
                    address: 'Укажите адрес доставки'
                };
                const name = el.getAttribute('name');
                if (map[name]) el.setCustomValidity(map[name]);
            }, true);

            // Сброс кастомных сообщений при вводе/изменении
            form.querySelectorAll('input,select,textarea').forEach((el) => {
                el.addEventListener('input', () => el.setCustomValidity(''));
                el.addEventListener('change', () => el.setCustomValidity(''));
            });
        });
    </script>
@endsection
