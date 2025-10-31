@extends('layouts.front')

@section('title', $title)
@if($desc) @section('meta_description', $desc) @endif
@section('content')
    <main class="pages">
        <div class="container">
            <nav class="breadcrumbs" aria-label="Хлебные крошки">
                <ol>
                    <li><a href="{{ route('front.home') }}">Главная</a></li>
                    @if($product->category)
                        <li><a href="{{ route('categories.index') }}">Категории</a></li>
                        <li><a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a></li>
                    @else
                        <li><a href="{{ route('categories.index') }}">Категории</a></li>
                    @endif
                    <li aria-current="page">{{ $h1 }}</li>
                </ol>
            </nav>
        </div>

        <div class="centeres">
            <section class="products-page">
                <span class="decor-text">Создаем комфорт</span>

                <section class="product-item">
                    <div class="container">
                        <div class="product-item__inner" data-code="{{ $product->sku ?? $product->slug ?? $product->id }}">
                            {{-- Галерея --}}
                            <div class="product-item__gallery">
                                <div class="product-item__hero swiper product-swiper">
                                    <div class="swiper-wrapper">
                                        @forelse($gallery as $img)
                                            <div class="swiper-slide">
                                                <a href="{{ $img }}" data-viewer data-caption="{{ $product->name }}">
                                                    <img src="{{ $img }}" alt="{{ $product->name }}">
                                                </a>
                                            </div>
                                        @empty
                                            @php $fallback = '/img/not.png'; @endphp
                                            <div class="swiper-slide">
                                                <a href="{{ $fallback }}" data-viewer data-caption="{{ $product->name }}">
                                                    <img src="{{ $fallback }}" alt="{{ $product->name }}">
                                                </a>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="product-item__thumbs swiper thumbs-swiper">
                                    <div class="swiper-wrapper">
                                        @forelse($thumbs as $img)
                                            <div class="swiper-slide"><img src="{{ $img }}" alt="thumb"></div>
                                        @empty
                                            <div class="swiper-slide"><img src="/img/not.png" alt="thumb"></div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            {{-- Инфо --}}
                            <div class="product-item__info">
                                <h1 class="product-item__title">{{ $product->name }}</h1>

                                @php
                                    $hasDiscount = $product->hasActiveDiscount();
                                    $finalPrice  = number_format($product->finalPrice(), 0, '.', ' ') . ' ₸';
                                    $basePrice   = number_format($product->price, 0, '.', ' ') . ' ₸';
                                    $colorRows   = $specs->where('code','color');
                                    $materialRow = $specs->firstWhere('code','material');
                                @endphp

                                <div class="product-item__price">
                                    @if($hasDiscount)
                                        <span class="product-item__oldprice" style="text-decoration:line-through; opacity:.6; margin-right:10px;">
                                        {{ $basePrice }}
                                    </span>
                                    @endif
                                    <span class="product-item__newprice">{{ $finalPrice }}</span>
                                </div>

                                @if($colorRows->isNotEmpty())
                                    <div class="product-item__color">
                                        <p>Цвет корпуса</p>
                                        <div class="product-item__color-list">
                                            @foreach($colorRows as $i => $row)
                                                @php $hex = $row['slug'] ?: null; $label = $row['value']; @endphp
                                                @if($hex)
                                                    <button class="color-btn {{ $i === 0 ? 'active' : '' }}" style="background: {{ $hex }};" title="{{ $label }}"></button>
                                                @else
                                                    <span class="color-label">{{ $label }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($materialRow)
                                    <div class="product-item__material">
                                        <p><strong>Материал корпуса:</strong></p>
                                        <p>{{ $materialRow['value'] }}</p>
                                    </div>
                                @endif

                                @isset($availability)
                                    @if(!empty($availability))
                                        <div class="product-item__availability">
                                            @php
                                                $preferred = ['Алматы','Астана'];
                                                $first = collect($preferred)
                                                    ->filter(fn($c)=>array_key_exists($c,$availability))
                                                    ->mapWithKeys(fn($c)=>[$c => (bool)$availability[$c]]);
                                                $rest = collect($availability)
                                                    ->reject(fn($v,$k)=>in_array($k,$preferred,true))
                                                    ->mapWithKeys(fn($v,$k)=>[$k => (bool)$v]);
                                                $ordered = $first->merge($rest);
                                            @endphp
                                            @foreach($ordered as $city => $in)
                                                <p>{{ $city }} - <span class="{{ $in ? 'yes' : 'no' }}">{{ $in ? 'есть в наличии' : 'нет в наличии' }}</span></p>
                                            @endforeach
                                        </div>
                                    @endif
                                @endisset

                                <button class="product-item__btn" type="button" {{ ($inStockSomewhere ?? true) ? '' : 'disabled' }}>
                                    {{ ($inStockSomewhere ?? true) ? 'Добавить в корзину' : 'Нет в наличии' }}
                                </button>
                            </div>
                        </div>

                        {{-- Табы --}}
                        <div class="product-item__tabs">
                            <div class="tabs__header">
                                <button class="tab-btn active" data-tab="desc">Описание</button>
                                <button class="tab-btn" data-tab="spec">Основные характеристики</button>
                                <button class="tab-btn" data-tab="reviews">Отзывы {{ $reviewsCount }}</button>
                            </div>

                            <div class="tabs__content">
                                <div class="tab-content tab-content-width active" id="desc">
                                    @if($product->description)
                                        {!! $product->description !!}
                                    @else
                                        <p>Описания пока нет.</p>
                                    @endif
                                </div>

                                <div class="tab-content tab-content-width" id="spec">
                                    @if($specs->isEmpty())
                                        <p>Характеристики будут добавлены позже.</p>
                                    @else
                                        <div class="specs">
                                            @foreach($specs as $row)
                                                <div class="specs__row">
                                                    <div class="specs__name">{{ $row['name'] }}</div>
                                                    <div class="specs__value">{{ $row['value'] }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="tab-content" id="reviews">
                                    <div class="reviews-section">
                                        {{-- Список отзывов --}}
                                        <div class="reviews-list">
                                            <h3>{{ $reviewsCount }} отзыв(ов)</h3>

                                            @forelse($reviews as $rev)
                                                <div class="review-item">
                                                    <div class="review-header">
                                                        <span class="review-author">{{ $rev->author_name ?: 'Гость' }}</span>
                                                        <span class="review-date">{{ optional($rev->created_at)->timezone(config('app.timezone'))->format('d.m.Y') }}</span>
                                                    </div>

                                                    <p class="review-text">{{ $rev->body }}</p>

                                                    @php $media = method_exists($rev,'getMedia') ? $rev->getMedia('photos') : collect(); @endphp
                                                    @if($media->count())
                                                        <div class="review-images">
                                                            @foreach($media as $m)
                                                                <a href="{{ $m->getUrl() }}" data-viewer data-caption="{{ $rev->author_name ?: 'Гость' }}">
                                                                    <img src="{{ $m->getUrl('thumb') }}" alt="Фото отзыва">
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @empty
                                                <p>Отзывов пока нет.</p>
                                            @endforelse
                                        </div>

                                        {{-- Форма отзыва (для авторизованных) --}}
                                        <div class="review-form">
                                            <div id="toast" class="toast" aria-live="polite" aria-atomic="true"></div>

                                            @if(session('ok'))
                                                <script>window.addEventListener('DOMContentLoaded',()=>showToast(@json(session('ok')),'success'));</script>
                                            @endif
                                            @if($errors->any())
                                                <script>window.addEventListener('DOMContentLoaded',()=>showToast(@json(implode("\n",$errors->all())),'error'));</script>
                                            @endif

                                            @auth
                                                <form action="{{ route('product.reviews.store', $product->slug) }}" method="post" enctype="multipart/form-data" class="review-form__body">
                                                    @csrf

                                                    <textarea name="body" class="review-input" placeholder="Напишите отзыв" required>{{ old('body') }}</textarea>

                                                    {{-- Превью выбранных фото + удаление до отправки --}}
                                                    <div id="uploadPreview" class="upload-preview" aria-live="polite"></div>

                                                    <div class="upload-box">
                                                        <div class="upload-icon">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M3 15V19C3 20.1 3.9 21 5 21H19C19.5304 21 20.0391 20.7893 20.4142 20.4142C20.7893 20.0391 21 19.5304 21 19V15M17 9L12 14L7 9M12 12.8V2.5" stroke="#6D031A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </div>
                                                        <p class="upload-text">Перетащите фото</p>
                                                        <input type="file" id="reviewPhotos" class="upload-input" name="photos[]" accept="image/*" multiple>
                                                    </div>

                                                    <button class="submit-btn" type="submit">Опубликовать комментарий</button>
                                                </form>
                                            @endauth

                                            @guest
                                                <div class="login-to-review" style="display:flex;flex-direction:column;gap:12px;align-items:start;">
                                                    <p>Чтобы оставить отзыв, <a href="{{ route('login') }}">войдите</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a>.</p>
                                                </div>
                                            @endguest
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Похожие --}}
                @if($similar->isNotEmpty())
                    <section class="similar">
                        <div class="container">
                            <div class="top">
                                <h2 class="section-title">Похожие товары</h2>
                                <div class="similar-btns">
                                    <div class="similar-btn similar-btn-prev">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12.75 6C13.1642 6 13.5 6.33579 13.5 6.75C13.5 7.16421 13.1642 7.5 12.75 7.5V6.75V6ZM1.60714 7.5C1.19293 7.5 0.857142 7.16421 0.857142 6.75C0.857142 6.33579 1.19293 6 1.60714 6V6.75V7.5ZM6.21967 0.21967C6.51256 -0.0732233 6.98744 -0.0732233 7.28033 0.21967C7.57322 0.512563 7.57322 0.987437 7.28033 1.28033L6.75 0.75L6.21967 0.21967ZM0.75 6.75L0.21967 7.28033C-0.0732231 6.98744 -0.0732231 6.51256 0.21967 6.21967L0.75 6.75ZM7.28033 12.2197C7.57322 12.5126 7.57322 12.9874 7.28033 13.2803C6.98744 13.5732 6.51256 13.5732 6.21967 13.2803L6.75 12.75L7.28033 12.2197ZM12.75 6.75V7.5H1.60714V6.75V6H12.75V6.75ZM6.75 0.75L7.28033 1.28033L1.28033 7.28033L0.75 6.75L0.21967 6.21967L6.21967 0.21967L6.75 0.75ZM0.75 6.75L1.28033 6.21967L7.28033 12.2197L6.75 12.75L6.21967 13.2803L0.21967 7.28033L0.75 6.75Z" fill="white"></path>
                                        </svg>
                                    </div>
                                    <div class="similar-btn similar-btn-next">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.75 6C0.335786 6 0 6.33579 0 6.75C0 7.16421 0.335786 7.5 0.75 7.5V6.75V6ZM11.8929 7.5C12.3071 7.5 12.6429 7.16421 12.6429 6.75C12.6429 6.33579 12.3071 6 11.8929 6V6.75V7.5ZM7.28033 0.21967C6.98744 -0.0732233 6.51256 -0.0732233 6.21967 0.21967C5.92678 0.512563 5.92678 0.987437 6.21967 1.28033L6.75 0.75L7.28033 0.21967ZM12.75 6.75L13.2803 7.28033C13.5732 6.98744 13.5732 6.51256 13.2803 6.21967L12.75 6.75ZM6.21967 12.2197C5.92678 12.5126 5.92678 12.9874 6.21967 13.2803C6.51256 13.5732 6.98744 13.5732 7.28033 13.2803L6.75 12.75L6.21967 12.2197ZM0.75 6.75V7.5H11.8929V6.75V6H0.75V6.75ZM6.75 0.75L6.21967 1.28033L12.2197 7.28033L12.75 6.75L13.2803 6.21967L7.28033 0.21967L6.75 0.75ZM12.75 6.75L12.2197 6.21967L6.21967 12.2197L6.75 12.75L7.28033 13.2803L13.2803 7.28033L12.75 6.75Z" fill="#6D031A"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="similar-swiper prod-items">
                            <div class="swiper-wrapper">
                                @foreach($similar as $p)
                                    @php
                                        $img      = $p->coverUrl('thumb') ?? '/img/not.png';
                                        $pHasDisc = $p->hasActiveDiscount();
                                        $pFinal   = $p->finalPrice();
                                        $pBase    = number_format($p->price, 0, '.', ' ') . ' ₸';
                                        $pPct     = ($pHasDisc && $p->price > 0)
                                            ? max(0, min(100, (int) round(100 - ($pFinal / $p->price * 100))))
                                            : null;
                                        $pInStock  = $p->inStockSomewhere();
                                    @endphp
                                    <div class="swiper-slide">
                                        <div class="prod-item" data-code="{{ $p->sku ?? $p->slug ?? $p->id }}">
                                            @if($pPct) <div class="procent">{{ $pPct }}%</div> @endif

                                            <a href="{{ route('product.show', $p->slug) }}" class="prod-link">
                                                <div class="prod-item__top">
                                                    <img src="{{ $img }}" alt="{{ $p->name }}">
                                                </div>
                                                <div class="prod-item__bottom">
                                                    <h3 class="prod-item__title">{{ $p->name }}</h3>
                                                    <p class="prod-item__text">
                                                        <span>{{ $pBase }}</span>
                                                    </p>
                                                </div>
                                            </a>

                                                <button
                                                    class="prod-item__btn {{ $pInStock ? '' : 'out-of-stock' }}"
                                                    type="button"
                                                    data-available="{{ $pInStock ? 1 : 0 }}"
                                                    {{ $pInStock ? '' : 'disabled' }}>
                                                    {{ $pInStock ? 'Добавить в корзину' : 'Нет в наличии' }}
                                                </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                {{-- С этим товаром рекомендуют --}}
                @if($recommended->isNotEmpty())
                    <section class="recomend">
                        <div class="container">
                            <div class="top">
                                <h2 class="section-title">С этим товаром рекомендуют</h2>
                                <div class="similar-btns">
                                    <div class="similar-btn recommend-btn-prev"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12.75 6C13.1642 6 13.5 6.33579 13.5 6.75C13.5 7.16421 13.1642 7.5 12.75 7.5V6.75V6ZM1.60714 7.5C1.19293 7.5 0.857142 7.16421 0.857142 6.75C0.857142 6.33579 1.19293 6 1.60714 6V6.75V7.5ZM6.21967 0.21967C6.51256 -0.0732233 6.98744 -0.0732233 7.28033 0.21967C7.57322 0.512563 7.57322 0.987437 7.28033 1.28033L6.75 0.75L6.21967 0.21967ZM0.75 6.75L0.21967 7.28033C-0.0732231 6.98744 -0.0732231 6.51256 0.21967 6.21967L0.75 6.75ZM7.28033 12.2197C7.57322 12.5126 7.57322 12.9874 7.28033 13.2803C6.98744 13.5732 6.51256 13.5732 6.21967 13.2803L6.75 12.75L7.28033 12.2197ZM12.75 6.75V7.5H1.60714V6.75V6H12.75V6.75ZM6.75 0.75L7.28033 1.28033L1.28033 7.28033L0.75 6.75L0.21967 6.21967L6.21967 0.21967L6.75 0.75ZM0.75 6.75L1.28033 6.21967L7.28033 12.2197L6.75 12.75L6.21967 13.2803L0.21967 7.28033L0.75 6.75Z" fill="white"></path>
                                        </svg></div>
                                    <div class="similar-btn recommend-btn-next"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.75 6C0.335786 6 0 6.33579 0 6.75C0 7.16421 0.335786 7.5 0.75 7.5V6.75V6ZM11.8929 7.5C12.3071 7.5 12.6429 7.16421 12.6429 6.75C12.6429 6.33579 12.3071 6 11.8929 6V6.75V7.5ZM7.28033 0.21967C6.98744 -0.0732233 6.51256 -0.0732233 6.21967 0.21967C5.92678 0.512563 5.92678 0.987437 6.21967 1.28033L6.75 0.75L7.28033 0.21967ZM12.75 6.75L13.2803 7.28033C13.5732 6.98744 13.5732 6.51256 13.2803 6.21967L12.75 6.75ZM6.21967 12.2197C5.92678 12.5126 5.92678 12.9874 6.21967 13.2803C6.51256 13.5732 6.98744 13.5732 7.28033 13.2803L6.75 12.75L6.21967 12.2197ZM0.75 6.75V7.5H11.8929V6.75V6H0.75V6.75ZM6.75 0.75L6.21967 1.28033L12.2197 7.28033L12.75 6.75L13.2803 6.21967L7.28033 0.21967L6.75 0.75ZM12.75 6.75L12.2197 6.21967L6.21967 12.2197L6.75 12.75L7.28033 13.2803L13.2803 7.28033L12.75 6.75Z" fill="#6D031A"></path>
                                        </svg></div>
                                </div>
                            </div>
                        </div>

                        <div class="recommend-swiper prod-items">
                            <div class="swiper-wrapper">
                                @foreach($recommended as $p)
                                    @php
                                        $img      = $p->coverUrl('thumb') ?? '/img/not.png';
                                        $pHasDisc = $p->hasActiveDiscount();
                                        $pFinal   = $p->finalPrice();
                                        $pBase    = number_format($p->price, 0, '.', ' ') . ' ₸';
                                        $pPct     = ($pHasDisc && $p->price > 0)
                                            ? max(0, min(100, (int) round(100 - ($pFinal / $p->price * 100))))
                                            : null;
                                                $pInStock  = $p->inStockSomewhere();
                                    @endphp
                                    <div class="swiper-slide">
                                        <div class="prod-item" data-code="{{ $p->sku ?? $p->slug ?? $p->id }}">
                                            @if($pPct) <div class="procent">{{ $pPct }}%</div> @endif

                                            <a href="{{ route('product.show', $p->slug) }}" class="prod-link">
                                                <div class="prod-item__top">
                                                    <img src="{{ $img }}" alt="{{ $p->name }}">
                                                </div>
                                                <div class="prod-item__bottom">
                                                    <h3 class="prod-item__title">{{ $p->name }}</h3>
                                                    <p class="prod-item__text">
                                                        <span>{{ $pBase }}</span>
                                                    </p>
                                                </div>
                                            </a>

                                                <button
                                                    class="prod-item__btn {{ $pInStock ? '' : 'out-of-stock' }}"
                                                    type="button"
                                                    data-available="{{ $pInStock ? 1 : 0 }}"
                                                    {{ $pInStock ? '' : 'disabled' }}>
                                                    {{ $pInStock ? 'Добавить в корзину' : 'Нет в наличии' }}
                                                </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            </section>
        </div>
    </main>


    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.umd.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                (function () {
                    const lb     = document.getElementById('imgLightbox');
                    const img    = document.getElementById('lbImg');
                    const cap    = document.getElementById('lbCap');
                    const header = document.getElementById('mainHeader');

                    if (!lb || !img) return;

                    const htmlEl = document.documentElement;

                    const open = (src, caption) => {
                        img.src = src;
                        img.alt = caption || '';
                        if (cap) cap.textContent = caption || '';

                        // сохраняем исходные стили
                        if (!lb.dataset.prevOverflow)
                            lb.dataset.prevOverflow = htmlEl.style.overflow || '';
                        if (header && !header.dataset.prevZ)
                            header.dataset.prevZ = header.style.zIndex || '';

                        // показываем лайтбокс
                        lb.hidden = false;
                        htmlEl.style.overflow = 'hidden';

                        // опускаем шапку вниз по z-index
                        if (header) header.style.zIndex = '5';
                    };

                    const close = () => {
                        lb.hidden = true;
                        img.src = '';
                        if (cap) cap.textContent = '';

                        // возвращаем всё обратно
                        htmlEl.style.overflow = lb.dataset.prevOverflow || '';
                        delete lb.dataset.prevOverflow;

                        if (header) {
                            header.style.zIndex = header.dataset.prevZ || '';
                            delete header.dataset.prevZ;
                        }
                    };

                    // Открытие
                    document.addEventListener('click', (e) => {
                        if (!lb.hidden && lb.contains(e.target)) return;

                        const trigger = e.target.closest('a[data-viewer], img[data-viewer]');
                        if (!trigger) return;

                        const src = trigger.tagName === 'A'
                            ? trigger.getAttribute('href')
                            : (trigger.getAttribute('data-src') || trigger.getAttribute('src'));
                        if (!src) return;

                        e.preventDefault();
                        const caption = trigger.getAttribute('data-caption') || trigger.getAttribute('alt') || '';
                        open(src, caption);
                    });

                    // Закрытие
                    lb.addEventListener('click', (e) => {
                        if (e.target.hasAttribute('data-close')) close();
                    });
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && !lb.hidden) close();
                    });
                })();



                const input = document.querySelector('.review-form .upload-input');
                if (!input) return;

                // Рамка, где рисуем превью
                let box = document.querySelector('#uploadPreview');
                if (!box) {
                    box = document.createElement('div');
                    box.id = 'uploadPreview';
                    box.style.cssText = 'display:flex;flex-wrap:wrap;gap:10px;margin-top:10px;';
                    input.closest('.upload-box').after(box);
                }

                const dt = new DataTransfer();

                function render() {
                    box.innerHTML = '';
                    Array.from(dt.files).forEach((file, idx) => {
                        const wrap = document.createElement('div');
                        wrap.style.cssText = 'position:relative;width:80px;height:80px;border:1px solid #eee;border-radius:8px;overflow:hidden;display:flex;align-items:center;justify-content:center;';

                        const img = document.createElement('img');
                        img.style.cssText = 'max-width:100%;max-height:100%;object-fit:cover;';
                        if (file.type.startsWith('image/')) {
                            const r = new FileReader();
                            r.onload = e => img.src = e.target.result;
                            r.readAsDataURL(file);
                        }
                        wrap.appendChild(img);

                        const del = document.createElement('button');
                        del.type = 'button';
                        del.textContent = '×';
                        del.style.cssText = 'position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;background:#fff;border:1px solid #ccc;line-height:18px;font-size:14px;';
                        del.onclick = () => {
                            const files = Array.from(dt.files);
                            files.splice(idx,1);
                            const ndt = new DataTransfer();
                            files.forEach(f => ndt.items.add(f));
                            input.files = ndt.files;
                            dt.items.clear();
                            Array.from(ndt.files).forEach(f => dt.items.add(f));
                            render();
                        };
                        wrap.appendChild(del);

                        box.appendChild(wrap);
                    });
                }

                input.addEventListener('change', () => {
                    // Максимум 5 файлов, только картинки
                    Array.from(input.files).forEach(f => {
                        if (!f.type.startsWith('image/')) return;
                        if (dt.files.length >= 5) return;
                        dt.items.add(f);
                    });
                    input.files = dt.files;
                    render();
                });
            });
        </script>
    @endpush

@endsection
