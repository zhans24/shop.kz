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
                        <li><a href="{{ route('category.show', $product->category->slug) }}">Категории</a></li>
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

                {{-- Детальная карточка --}}
                <section class="product-item">
                    <div class="container">
                        <div class="product-item__inner">
                            {{-- Галерея (классы под твой index.js: .product-swiper + .thumbs-swiper) --}}
                            <div class="product-item__gallery">
                                <div class="product-item__hero swiper product-swiper">
                                    <div class="swiper-wrapper">
                                        @forelse($gallery as $img)
                                            <div class="swiper-slide">
                                                <img src="{{ $img }}" alt="{{ $product->name }}">
                                            </div>
                                        @empty
                                            <div class="swiper-slide">
                                                <img src="/img/no-image.webp" alt="{{ $product->name }}">
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="product-item__thumbs swiper thumbs-swiper">
                                    <div class="swiper-wrapper">
                                        @forelse($thumbs as $img)
                                            <div class="swiper-slide">
                                                <img src="{{ $img }}" alt="thumb">
                                            </div>
                                        @empty
                                            <div class="swiper-slide">
                                                <img src="/img/no-image.webp" alt="thumb">
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            {{-- Инфо --}}
                            <div class="product-item__info">
                                <h1 class="product-item__title">{{ $product->name }}</h1>

                                @php
                                    $final = number_format($product->finalPrice(), 0, '.', ' ') . ' ₸';
                                    $hasDiscount = $product->hasActiveDiscount();
                                @endphp

                                <div class="product-item__price">
                                    @if($hasDiscount)
                                        <span class="procent">{{ (int)$product->discount_percent }}%</span>
                                    @endif
                                    {{ $final }}
                                </div>

                                {{-- Пример вывода 1-2 «витринных» характеристик, если есть --}}
                                @if($specs->isNotEmpty())
                                    @php
                                        // подставим наиболее «человечные» поля, если такие попадутся
                                        $color = $specs->firstWhere('name', fn($n)=>mb_stripos($n,'цвет')!==false);
                                        $material = $specs->firstWhere('name', fn($n)=>mb_stripos($n,'материал')!==false);
                                    @endphp

                                    @if($color)
                                        <div class="product-item__color">
                                            <p>Цвет корпуса</p>
                                            <div class="product-item__color-list">
                                                {{-- В мокете у тебя круги; здесь нет палитры, так что просто текст --}}
                                                <span class="color-label">{{ $color['value'] }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($material)
                                        <div class="product-item__material">
                                            <p><strong>Материал корпуса:</strong></p>
                                            <p>{{ $material['value'] }}</p>
                                        </div>
                                    @endif
                                @endif

                                {{-- Наличие — нет данных в моделях, оставим нейтральный блок/скрой если не нужен --}}
                                {{-- <div class="product-item__availability">
                                    <p>Алматы - <span class="yes">есть в наличии</span></p>
                                    <p>Астана - <span class="no">нет в наличии</span></p>
                                </div> --}}

                                <form action="{{ route('cart.add', $product->id ?? 0) }}" method="post">
                                    @csrf
                                    <button class="product-item__btn" type="submit">Добавить в корзину</button>
                                </form>
                            </div>
                        </div>

                        {{-- Табы --}}
                        <div class="product-item__tabs">
                            <div class="tabs__header">
                                <button class="tab-btn active" data-tab="desc">Описание</button>
                                <button class="tab-btn" data-tab="spec">Основные характеристики</button>
                                <button class="tab-btn" data-tab="reviews">
                                    Отзывы {{ $product->reviews?->count() ?? 0 }}
                                </button>
                            </div>

                            <div class="tabs__content">
                                {{-- Описание --}}
                                <div class="tab-content tab-content-width active" id="desc">
                                    @if($product->description)
                                        {!! nl2br(e($product->description)) !!}
                                    @else
                                        <p>Описания пока нет.</p>
                                    @endif
                                </div>

                                {{-- Характеристики --}}
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

                                {{-- Отзывы --}}
                                <div class="tab-content" id="reviews">
                                    @php $r = $product->reviews; @endphp
                                    <div class="reviews-section">
                                        <div class="reviews-list">
                                            <h3>{{ $r?->count() ?? 0 }} отзыв(ов)</h3>

                                            @forelse($r ?? [] as $rev)
                                                <div class="review-item">
                                                    <div class="review-header">
                                                        <span class="review-author">{{ $rev->author_name ?? 'Аноним' }}</span>
                                                        <span class="review-date">{{ optional($rev->created_at)->format('d.m.Y') }}</span>
                                                    </div>
                                                    <p class="review-text">{{ $rev->content ?? $rev->text ?? '' }}</p>
                                                </div>
                                            @empty
                                                <p>Отзывов пока нет.</p>
                                            @endforelse

                                            {{-- твоя кнопка «Показать больше комментариев» будет работать с тем же JS --}}
                                            @if(($r?->count() ?? 0) > 3)
                                                <a href="#!" class="show-more">Показать больше комментариев</a>
                                            @endif
                                        </div>

                                        {{-- форма отзыва — если нужна, повесь action --}}
                                        {{-- <div class="review-form">...</div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Похожие товары (слайдер под твои классы .similar-swiper) --}}
                @if($similar->isNotEmpty())
                    <section class="similar">
                        <div class="container">
                            <div class="top">
                                <h2 class="section-title">Похожие товары</h2>
                                <div class="similar-btns">
                                    <div class="similar-btn similar-btn-prev">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.75 6C13.1642 6 13.5 6.33579 13.5 6.75C13.5 7.16421 13.1642 7.5 12.75 7.5V6.75V6ZM1.60714 7.5C1.19293 7.5 0.857142 7.16421 0.857142 6.75C0.857142 6.33579 1.19293 6 1.60714 6V6.75V7.5ZM6.21967 0.21967C6.51256 -0.0732233 6.98744 -0.0732233 7.28033 0.21967C7.57322 0.512563 7.57322 0.987437 7.28033 1.28033L6.75 0.75L6.21967 0.21967ZM0.75 6.75L0.21967 7.28033C-0.0732231 6.98744 -0.0732231 6.51256 0.21967 6.21967L0.75 6.75ZM7.28033 12.2197C7.57322 12.5126 7.57322 12.9874 7.28033 13.2803C6.98744 13.5732 6.51256 13.5732 6.21967 13.2803L6.75 12.75L7.28033 12.2197ZM12.75 6.75V7.5H1.60714V6.75V6H12.75V6.75ZM6.75 0.75L7.28033 1.28033L1.28033 7.28033L0.75 6.75L0.21967 6.21967L6.21967 0.21967L6.75 0.75ZM0.75 6.75L1.28033 6.21967L7.28033 12.2197L6.75 12.75L6.21967 13.2803L0.21967 7.28033L0.75 6.75Z" fill="white"/></svg>
                                    </div>
                                    <div class="similar-btn similar-btn-next">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.75 6C0.335786 6 0 6.33579 0 6.75C0 7.16421 0.335786 7.5 0.75 7.5V6.75V6ZM11.8929 7.5C12.3071 7.5 12.6429 7.16421 12.6429 6.75C12.6429 6.33579 12.3071 6 11.8929 6V6.75V7.5ZM7.28033 0.21967C6.98744 -0.0732233 6.51256 -0.0732233 6.21967 0.21967C5.92678 0.512563 5.92678 0.987437 6.21967 1.28033L6.75 0.75L7.28033 0.21967ZM12.75 6.75L13.2803 7.28033C13.5732 6.98744 13.5732 6.51256 13.2803 6.21967L12.75 6.75ZM6.21967 12.2197C5.92678 12.5126 5.92678 12.9874 6.21967 13.2803C6.51256 13.5732 6.98744 13.5732 7.28033 13.2803L6.75 12.75L6.21967 12.2197ZM0.75 6.75V7.5H11.8929V6.75V6H0.75V6.75ZM6.75 0.75L6.21967 1.28033L12.2197 7.28033L12.75 6.75L13.2803 6.21967L7.28033 0.21967L6.75 0.75ZM12.75 6.75L12.2197 6.21967L6.21967 12.2197L6.75 12.75L7.28033 13.2803L13.2803 7.28033L12.75 6.75Z" fill="#6D031A"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="similar-swiper prod-items">
                                <div class="swiper-wrapper">
                                    @foreach($similar as $p)
                                        @php
                                            $img = $p->coverUrl('thumb');
                                            $link = route('product.show', $p->slug);
                                        @endphp
                                        <div class="swiper-slide">
                                            <div class="prod-item">
                                                @if($p->hasActiveDiscount() && $p->discount_percent)
                                                    <div class="procent">{{ (int)$p->discount_percent }}%</div>
                                                @endif
                                                <a href="{{ $link }}" class="prod-link">
                                                    <div class="prod-item__top">
                                                        <img src="{{ $img ?? '/img/no-image.webp' }}" alt="{{ $p->name }}">
                                                    </div>
                                                    <div class="prod-item__bottom">
                                                        <h3 class="prod-item__title">{{ $p->name }}</h3>
                                                        <p class="prod-item__text">{{ number_format($p->finalPrice(), 0, '.', ' ') }} ₸</p>
                                                    </div>
                                                </a>
                                                <form action="{{ route('cart.add', $p->id ?? 0) }}" method="post">
                                                    @csrf
                                                    <button class="prod-item__btn" type="submit">Добавить в корзину</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </section>
                @endif
            </section>
        </div>
    </main>
@endsection
