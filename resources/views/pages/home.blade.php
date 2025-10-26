{{-- resources/views/pages/home.blade.php --}}
@extends('layouts.front')

@php
    $page    = $page    ?? null;   // ← чтобы не было "Undefined variable", даже если забудешь передать
    $homeDto = $homeDto ?? ['exists'=>false,'meta'=>[]];

    // SEO: приоритет — seo_meta → поля Page → dto → дефолт
    $seo   = $page?->seo;
    $h1    = $seo->h1 ?? ($homeDto['title'] ?? null);
    $title = $seo->meta_title
           ?? $page?->meta_title
           ?? ($homeDto['meta']['title'] ?? 'TechnoStyle');
    $desc  = $seo->meta_description
           ?? $page?->meta_description
           ?? ($homeDto['meta']['description'] ?? null);
@endphp


@section('title', $title)
@if($desc) @section('meta_description', $desc) @endif

@section('content')
    {{-- HERO --}}
    @if(($homeDto['exists'] ?? false) && !empty($homeDto['slides']))
        <section class="hero container">
            <div class="hero__left"></div>
            <div class="hero__right"></div>

            <div class="hero__slider hero-slider swiper">
                @if(!empty($homeDto['decor_text']))
                    <span class="decor-text">{{ $homeDto['decor_text'] }}</span>
                @endif

                <div class="swiper-wrapper">
                    @foreach($homeDto['slides'] as $i => $s)
                        <div class="swiper-slide">
                            <div class="hero__slide">
                                <div class="hero__img">
                                    @if($s['left_url'])
                                        <img class="bg-img" src="{{ $s['left_url'] }}" alt="" loading="lazy" decoding="async">
                                    @endif
                                </div>
                                <div class="hero__text">
                                    @if($i===0 && $h1)
                                        <h1>{{ $h1 }}</h1>
                                    @elseif(!empty($s['title']))
                                        <h1>{{ $s['title'] }}</h1>
                                    @endif
                                    @if(!empty($s['text']))
                                        <p>{{ $s['text'] }}</p>
                                    @endif
                                </div>
                                <div class="hero__img">
                                    @if($s['right_url'])
                                        <img class="bg-img" src="{{ $s['right_url'] }}" alt="" loading="lazy" decoding="async">
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="swiper-pagination"></div>
                <div class="hero-swiper-button-wrap">
                    <div class="hero-swiper-button hero-swiper-button-prev">
                        {{-- твой SVG как в верстке --}}
                        <svg width="40" height="39" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M26 18.75c.414 0 .75.336.75.75s-.336.75-.75.75V19.5v-.75ZM14.857 20.25c-.414 0-.75-.336-.75-.75s.336-.75.75-.75V19.5v.75Zm4.619-7.137a.75.75 0 0 1 1.06 1.061L20 13.65l-.524-.537ZM14 19.5l-.524.537a.75.75 0 0 1 0-1.074L14 19.5Zm6.524 5.313a.75.75 0 1 1-1.048 1.074L20 25.35l.524-.537ZM26 19.5v.75H14.857V19.5v-.75H26Zm-6-5.85.524.537-6 5.887L14 19.5l.524-.537 5.476-5.85ZM14 19.5l.524-.537 6 5.887L20 25.35l-.524.537L14 19.5Z" fill="white"/><path d="M20 .5C30.782.5 39.5 9.018 39.5 19.5S30.782 38.5 20 38.5  .5 29.982.5 19.5 9.218.5 20 .5Z" fill="black" fill-opacity=".01" stroke="#F6F3E4"/></svg>
                    </div>
                    <div class="hero-swiper-button hero-swiper-button-next">
                        {{-- твой SVG как в верстке --}}
                        <svg width="40" height="39" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 .5C30.782.5 39.5 9.018 39.5 19.5S30.782 38.5 20 38.5  .5 29.982.5 19.5 9.218.5 20 .5Z" fill="black" fill-opacity=".01" stroke="#F6F3E4"/><path d="M14 18.75c-.414 0-.75.336-.75.75s.336.75.75.75V19.5v-.75ZM25.143 20.25c.414 0 .75-.336.75-.75s-.336-.75-.75-.75V19.5v.75Zm-4.62-7.137a.75.75 0 0 0-1.06 1.061L20 13.65l.524-.537ZM26 19.5l.524.537a.75.75 0 0 0 0-1.074L26 19.5Zm-6.524 5.313a.75.75 0 0 0 1.048 1.074L20 25.35l-.524.537ZM14 19.5v.75h11.143V19.5v-1.5H14Zm6-5.85-1.048 1.074 5.999 5.776L26 19.5l.524-.537-6-5.85ZM26 19.5l-.524.537-6 5.776L20 25.35l1.048 1.074L26 19.5Z" fill="white"/></svg>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <div class="centeres">
        {{-- CALL BTNS --}}
        <div class="call-btns">
            <a class="call" href="#!"><img src="{{ asset('img/icons/phone.svg') }}" alt="phone"></a>
            <a class="call" href="#!"><img src="{{ asset('img/icons/whatsapp.svg') }}" alt="whatsapp"></a>
        </div>

        {{-- КАТЕГОРИИ --}}
        @if($categories->count())
            <section class="categories">
                <span>Создаем комфорт</span>
                <div class="categories__wrapper container">
                    <h2 class="title">Категории товаров</h2>
                    <div class="categories__wrapp">
                        @foreach($categories as $cat)
                            @php $img = $cat->getFirstMediaUrl('image','thumb') ?: $cat->getFirstMediaUrl('image'); @endphp
                            <a href="{{ route('category.show', $cat->slug ?? '#') }}" class="categories__card">
                                <h3>{{ $cat->name }}</h3>
                                @if($img)<img src="{{ $img }}" alt="{{ $cat->name }}">@endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- ХИТЫ (только имя + картинка) --}}
        @if($hits->count())
            <section class="hits">
                <img class="bg-img" src="{{ asset('img/hits-bg.svg') }}" alt="">
                <h2 class="title">Хиты продаж</h2>
                <div class="swiper hits__slider">
                    <div class="swiper-wrapper">
                        @foreach($hits as $p)
                            <div class="swiper-slide">
                                <div class="hits__slide">
                                    <a href="{{ route('product.show', $p->slug ?? '#') }}" class="hits__card">
                                        <div class="hits__tag">ХИТ</div>
                                        <div class="hits__img">
                                            @if($p->coverUrl('thumb')) <img src="{{ $p->coverUrl('thumb') }}" alt="{{ $p->name }}"> @endif
                                        </div>
                                        <h3>{{ $p->name }}</h3>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="hits__navigation">
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    </div>
                </div>
            </section>
        @endif

        {{-- НОВИНКИ --}}
        @if($newest->count())
            <section class="novelty">
                <span>Создаем комфорт</span>
                <div class="novelty__wrapper container">
                    <h2 class="title">Новинки</h2>
                    <div class="novelty__wrapp">
                        @foreach($newest as $p)
                            <a href="{{ route('product.show', $p->slug ?? '#') }}" class="novelty__card">
                                <div class="novelty__img">
                                    @if($p->coverUrl('large')) <img class="bg-img" src="{{ $p->coverUrl('large') }}" alt="{{ $p->name }}"> @endif
                                </div>
                                <div class="novelty__text">
                                    <h3>{{ $p->name }}</h3>
                                    {{-- стрелочный круг из верстки --}}
                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="20" cy="20" r="20" transform="matrix(-1 0 0 1 40 0)" fill="#6D031A"/><path d="M14 19.25c-.414 0-.75.336-.75.75s.336.75.75.75V20v-.75Zm11.143 1.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75V20v.75Zm-4.613-7.28a.75.75 0 0 0-1.061 1.061L20 14l.53-.53ZM26 20l.53.53a.75.75 0 0 0 0-1.06L26 20Zm-6.53 6.53a.75.75 0 0 0 1.06 1.06L20 26l-.53.53ZM14 20v.75h11.143V20v-1.5H14V20Zm6-6-1.06 1.06L25.47 20.53 26 20l.53-.53L20 14Zm6 6-.53.53-6 6L20 26l.53.53 6-6Z" fill="white"/></svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <a href="{{ route('categories.index') }}" class="btn-hov">Показать все</a>               </div>
            </section>
        @endif

        {{-- АКЦИИ (ВАЖНО: .product-card.discount + .badge, как ты прислал) --}}
        @if($promos->count())
            <section class="stock hits">
                <h2 class="title">Акции и спецпредложения</h2>
                <div class="swiper hits__slider">
                    <div class="swiper-wrapper">
                        @foreach($promos as $p)
                            <div class="swiper-slide">
                                <div class="hits__slide">
                                    <a href="{{ route('product.show', $p->slug ?? '#') }}" class="product-card discount">
                                        @if($p->hasActiveDiscount() && $p->discount_percent)
                                            <span class="badge">{{ (int)$p->discount_percent }}%</span>
                                        @endif
                                        <div class="product-card-top">
                                            @if($p->coverUrl('thumb')) <img src="{{ $p->coverUrl('thumb') }}" alt="{{ $p->name }}"> @endif
                                        </div>
                                        <div class="prod-bottom">
                                            <h3>{{ $p->name }}</h3>
                                            <p class="price">
                                                {{ number_format($p->finalPrice(), 0, '.', ' ') }} ₸
                                            </p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="hits__navigation">
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    </div>
                </div>
            </section>
        @endif

        {{-- НОВОСТИ (как в твоей верстке) --}}
        @if($news->count())
            <section class="news">
                <span>Создаем комфорт</span>
                <div class="novelty__wrapper container">
                    <h2 class="title">Новости</h2>
                    <div class="novelty__wrapp">
                        @foreach($news as $post)
                            @php $img = $post->getFirstMediaUrl('cover','thumb') ?: $post->getFirstMediaUrl('cover'); @endphp
                            <a href="{{ route('news.show', $post->slug ?? '#') }}" class="novelty__card news__card">
                                <div class="novelty__img">@if($img)<img class="bg-img" src="{{ $img }}" alt="{{ $post->title }}">@endif</div>
                                <h3>{{ $post->title }}</h3>
                                @if($post->excerpt)<p>{{ $post->excerpt }}</p>@endif
                                <div class="novelty__text">
                                    <span>{{ optional($post->published_at)->format('d.m.Y') }}</span>
                                    <span>
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="8" cy="8" r="8" transform="matrix(-1 0 0 1 17 1)" stroke="#605E5D"/><path d="M9 6V10H12.5" stroke="#605E5D"/></svg>
                                    {{ optional($post->published_at)->format('H:i') }}
                                </span>
                                    {{-- стрелочный круг --}}
                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="20" cy="20" r="20" transform="matrix(-1 0 0 1 40 0)" fill="#6D031A"/><path d="M14 19.25c-.414 0-.75.336-.75.75s.336.75.75.75V20v-.75Zm11.143 1.5c.414 0 .75-.336.75-.75s-.336-.75-.75-.75V20v.75Zm-4.613-7.28a.75.75 0 0 0-1.061 1.061L20 14l.53-.53ZM26 20l.53.53a.75.75 0 0 0 0-1.06L26 20Zm-6.53 6.53a.75.75 0 0 0 1.06 1.06L20 26l-.53.53ZM14 20v.75h11.143V20v-1.5H14V20Zm6-6-1.06 1.06L25.47 20.53 26 20l.53-.53L20 14Zm6 6-.53.53-6 6L20 26l.53.53 6-6Z" fill="white"/></svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection
