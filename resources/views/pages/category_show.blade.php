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
                                        'type'        => 'По типу',
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
                                        <li data-value="type">По типу</li>
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
                                        <input type="number" id="minPrice" name="price_min" value="{{ $min }}">
                                        <input type="number" id="maxPrice" name="price_max" value="{{ $max }}">
                                    </div>
                                    <div class="slider">
                                        <div class="progress" style="left: {{ ($min/$limit)*100 }}%; right: {{ 100 - ($max/$limit)*100 }}%;"></div>
                                        <input type="range" id="rangeMin" min="0" max="{{ $limit }}" value="{{ $min }}" step="1000">
                                        <input type="range" id="rangeMax" min="0" max="{{ $limit }}" value="{{ $max }}" step="1000">
                                    </div>
                                </div>

                                <button class="filter-btn" type="submit">Поиск</button>
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
                                    $priceText = number_format($p->finalPrice(), 0, '.', ' ') . ' ₸';
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
        document.addEventListener('DOMContentLoaded', function () {
            // --- текст => id/slug
            const brandText = document.getElementById('brandText');
            const typeText  = document.getElementById('typeText');
            const brandId   = document.getElementById('brandId');
            const typeSlug  = document.getElementById('typeSlug');

            function mapBrand() {
                const val = (brandText.value || '').trim().toLowerCase();
                let id = '';
                document.querySelectorAll('#brandList option').forEach(o => {
                    if ((o.value || '').trim().toLowerCase() === val) { id = o.dataset.id || ''; }
                });
                brandId.value = id; // пусто — значит не фильтруем по бренду
            }

            function mapType() {
                const val = (typeText.value || '').trim().toLowerCase();
                let slug = '';
                document.querySelectorAll('#typeList option').forEach(o => {
                    if ((o.value || '').trim().toLowerCase() === val) { slug = o.dataset.slug || ''; }
                });
                typeSlug.value = slug; // пусто — значит не фильтруем по типу
            }

            brandText && brandText.addEventListener('change', mapBrand);
            typeText  && typeText.addEventListener('change', mapType);

            // перед сабмитом — финальная синхронизация
            const filterForm = document.getElementById('filterForm');
            if (filterForm) {
                filterForm.addEventListener('submit', function () {
                    mapBrand(); mapType();
                });
            }

            // --- страховка для прогресса (если стили обнулили)
            const rMin = document.getElementById('rangeMin');
            const rMax = document.getElementById('rangeMax');
            const iMin = document.getElementById('minPrice');
            const iMax = document.getElementById('maxPrice');
            const progress = document.querySelector('.price-range .progress');
            const maxAll = rMax ? parseInt(rMax.max,10) : 345000;
            function syncBar(){
                if (!progress || !rMin || !rMax) return;
                const mn = Math.max(0, parseInt(rMin.value||0,10));
                const mx = Math.max(0, parseInt(rMax.value||0,10));
                progress.style.left  = (mn / maxAll) * 100 + '%';
                progress.style.right = 100 - (mx / maxAll) * 100 + '%';
            }
            syncBar();
            ['input','change'].forEach(ev=>{
                rMin && rMin.addEventListener(ev, syncBar);
                rMax && rMax.addEventListener(ev, syncBar);
            });
        });

        (function(){
            function makeSuggest(input, optionsSelector, onPick){
                if(!input) return;
                input.setAttribute('autocomplete','off');

                const box = document.createElement('ul');
                box.className = 'filter-suggest';
                document.body.appendChild(box);

                const all = Array.from(document.querySelectorAll(optionsSelector)).map(o => ({
                    text: (o.value || '').trim(),
                    dataset: o.dataset
                }));

                function position(){
                    const r = input.getBoundingClientRect();
                    box.style.left = (window.scrollX + r.left) + 'px';
                    box.style.top  = (window.scrollY + r.bottom + 4) + 'px';
                    box.style.width = r.width + 'px';
                }

                function render(list){
                    box.innerHTML = '';
                    list.forEach(row=>{
                        const li = document.createElement('li');
                        li.textContent = row.text;
                        li.addEventListener('click', ()=>{
                            input.value = row.text;
                            onPick(row);
                            hide();
                            input.dispatchEvent(new Event('change'));
                        });
                        box.appendChild(li);
                    });
                    box.style.display = list.length ? 'block' : 'none';
                    position();
                }

                function hide(){ box.style.display = 'none'; }

                function filter(){
                    const v = (input.value||'').trim().toLowerCase();
                    const list = v ? all.filter(x => x.text.toLowerCase().includes(v)) : all;
                    render(list.slice(0,200));
                }

                input.addEventListener('focus', filter);
                input.addEventListener('input', filter);
                window.addEventListener('resize', position);
                window.addEventListener('scroll', position, true);
                document.addEventListener('click', (e)=>{ if(e.target!==input && !box.contains(e.target)) hide(); });
            }

            const brandText = document.getElementById('brandText');
            const typeText  = document.getElementById('typeText');
            const brandId   = document.getElementById('brandId');
            const typeSlug  = document.getElementById('typeSlug');

            makeSuggest(brandText, '#brandList option', (row)=>{
            brandId.value = row.dataset.id || '';
        });

            makeSuggest(typeText, '#typeList option', (row)=>{
            typeSlug.value = row.dataset.slug || '';
        });
        })();


    </script>
@endsection
