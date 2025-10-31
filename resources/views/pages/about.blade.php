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
                                @if(!empty($video))
                                    <video
                                        autoplay
                                        muted
                                        loop
                                        playsinline
                                        preload="auto"
                                    >
                                        <source src="{{ $video }}" type="video/mp4">
                                    </video>
                                @elseif(!empty($image))
                                    <img src="{{ $image }}" alt="О компании">
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
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.75 6C13.1642 6 13.5 6.33579 13.5 6.75C13.5 7.16421 13.1642 7.5 12.75 7.5V6.75V6ZM1.60714 7.5C1.19293 7.5 0.857142 7.16421 0.857142 6.75C0.857142 6.33579 1.19293 6 1.60714 6V6.75V7.5ZM6.21967 0.21967C6.51256 -0.0732233 6.98744 -0.0732233 7.28033 0.21967C7.57322 0.512563 7.57322 0.987437 7.28033 1.28033L6.75 0.75L6.21967 0.21967ZM0.75 6.75L0.21967 7.28033C-0.0732231 6.98744 -0.0732231 6.51256 0.21967 6.21967L0.75 6.75ZM7.28033 12.2197C7.57322 12.5126 7.57322 12.9874 7.28033 13.2803C6.98744 13.5732 6.51256 13.5732 6.21967 13.2803L6.75 12.75L7.28033 12.2197ZM12.75 6.75V7.5H1.60714V6.75V6H12.75V6.75ZM6.75 0.75L7.28033 1.28033L1.28033 7.28033L0.75 6.75L0.21967 6.21967L6.21967 0.21967L6.75 0.75ZM0.75 6.75L1.28033 6.21967L7.28033 12.2197L6.75 12.75L6.21967 13.2803L0.21967 7.28033L0.75 6.75Z" fill="white"></path>
                                </svg>
                            </button>
                            <button class="similar-btn reviews-btn-next">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.75 6C0.335786 6 0 6.33579 0 6.75C0 7.16421 0.335786 7.5 0.75 7.5V6.75V6ZM11.8929 7.5C12.3071 7.5 12.6429 7.16421 12.6429 6.75C12.6429 6.33579 12.3071 6 11.8929 6V6.75V7.5ZM7.28033 0.21967C6.98744 -0.0732233 6.51256 -0.0732233 6.21967 0.21967C5.92678 0.512563 5.92678 0.987437 6.21967 1.28033L6.75 0.75L7.28033 0.21967ZM12.75 6.75L13.2803 7.28033C13.5732 6.98744 13.5732 6.51256 13.2803 6.21967L12.75 6.75ZM6.21967 12.2197C5.92678 12.5126 5.92678 12.9874 6.21967 13.2803C6.51256 13.5732 6.98744 13.5732 7.28033 13.2803L6.75 12.75L6.21967 12.2197ZM0.75 6.75V7.5H11.8929V6.75V6H0.75V6.75ZM6.75 0.75L6.21967 1.28033L12.2197 7.28033L12.75 6.75L13.2803 6.21967L7.28033 0.21967L6.75 0.75ZM12.75 6.75L12.2197 6.21967L6.21967 12.2197L6.75 12.75L7.28033 13.2803L13.2803 7.28033L12.75 6.75Z" fill="#6D031A"></path>
                                </svg>
                            </button>
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
