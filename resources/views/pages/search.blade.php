@extends('layouts.front')

@section('title', $title)
@if($desc) @section('meta_description', $desc) @endif

@section('content')
    <main class="pages">
        <div class="container">
            <nav class="breadcrumbs" aria-label="Хлебные крошки">
                <ol>
                    <li><a href="{{ route('front.home') }}">Главная</a></li>
                    <li aria-current="page">Поиск</li>
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
                        </div>

                        <div class="product-grid">
                            @forelse($products as $p)
                                @php
                                    $img = $p->coverUrl('thumb') ?? '/img/not.png';
                                    $priceText = number_format($p->finalPrice(), 0, '.', ' ') . ' ₸';
                                @endphp

                                <a href="{{ route('product.show', $p->slug) }}" class="product-card" id="p-{{ $p->id }}">
                                    <div class="product-card-top">
                                        <img src="{{ $img }}" alt="{{ $p->name }}" loading="lazy" decoding="async" />
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

                        @if($products->hasPages())
                            <ul class="paginations">
                                <li>
                                    @if($products->onFirstPage())
                                        <span class="page-btn prev disabled">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10.06 12L11 11.06L7.94667 8L11 4.94L10.06 4L6.06 8L10.06 12Z" fill="black"/></svg>
                                    </span>
                                    @else
                                        <a href="{{ $products->previousPageUrl() }}" class="page-btn prev">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10.06 12L11 11.06L7.94667 8L11 4.94L10.06 4L6.06 8L10.06 12Z" fill="black"/></svg>
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
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6.94 4L6 4.94L9.05333 8L6 11.06L6.94 12L10.94 8L6.94 4Z" fill="black"/></svg>
                                        </a>
                                    @else
                                        <span class="page-btn next disabled">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6.94 4L6 4.94L9.05333 8L6 11.06L6.94 12L10.94 8L6.94 4Z" fill="black"/></svg>
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
@endsection
