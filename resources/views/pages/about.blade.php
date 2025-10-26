@extends('layouts.front')

@section('title', $meta['title'] ?? $title ?? 'TechnoStyle')
@if(!empty($meta['description']))
    @section('meta_description', $meta['description'])
@endif

@section('content')
    <main class="pages about-page">
        <div class="container">
            <nav class="breadcrumbs" aria-label="Хлебные крошки">
                <ol>
                    <li><a href="{{ route('front.home') }}">Главная</a></li>
                    <li aria-current="page">{{ $title ?? 'О компании' }}</li>
                </ol>
            </nav>
        </div>

        <div class="centeres">
            <div class="products-page">
                @if(!empty($decor_text))
                    <span class="decor-text">{{ $decor_text }}</span>
                @endif

                <section class="about">
                    <div class="container">
                        <div class="about__inner">
                            <div class="about__video">
                                @if(!empty($image))
                                    <img src="{{ $image }}" alt="">
                                @endif
                            </div>
                            <div class="about__desc">
                                <h2 class="about__title">{{ data_get($about,'title',$title) }}</h2>
                                @if(!empty($about['text']))
                                    <p class="about__text">{{ $about['text'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <article class="article">
        <div class="container">
            @if(!empty($benefits))
                <section class="benefits">
                    <div class="benefits__line">
                        <svg width="1241" height="12" viewBox="0 0 1241 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="99.5" cy="6" r="6" fill="#6D031A" />
                            <circle cx="435.5" cy="6" r="6" fill="#6D031A" />
                            <circle cx="740.5" cy="6" r="6" fill="#6D031A" />
                            <circle cx="1073.5" cy="6" r="6" fill="#6D031A" />
                            <path d="M1 6.5H1240" stroke="#6D031A" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>

                    <div class="benefits__items">
                        @foreach($benefits as $b)
                            <div class="benefits__item">
                                <h3 class="benefits__title">{{ $b['title'] ?? '' }}</h3>
                                <p class="benefits__text">{{ $b['text'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        @if(!empty($reviews))
            <section class="reviews">
                <div class="container">
                    <div class="reviews__top">
                        <h2 class="reviews-title">Отзывы</h2>
                        <div class="similar-btns">
                            <button class="similar-btn reviews-btn-prev">
                                {{-- стрелка влево — можно поставить svg как в макете --}}
                                ‹
                            </button>
                            <button class="similar-btn reviews-btn-next">›</button>
                        </div>
                    </div>
                </div>

                <div class="reviews-swiper swiper">
                    <div class="swiper-wrapper">
                        @foreach($reviews as $r)
                            <div class="reviews__slide swiper-slide">
                                <div class="reviews__img">
                                    @if(!empty($r['avatar']))
                                        <img src="{{ $r['avatar'] }}" alt="">
                                    @endif
                                </div>
                                <h3 class="reviews__title">{{ $r['name'] }}</h3>
                                <p class="reviews__text">{{ $r['text'] }}</p>
                                <img class="reviews__quote" src="{{ asset('img/quote.png') }}" alt="">
                            </div>
                        @endforeach
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        new Swiper('.reviews-swiper', {
                            slidesPerView: 1, spaceBetween: 16,
                            navigation: { nextEl: '.reviews-btn-next', prevEl: '.reviews-btn-prev' },
                            breakpoints: { 768:{slidesPerView:2}, 1024:{slidesPerView:3} }
                        });
                    });
                </script>
            </section>
        @endif
    </article>
@endsection
