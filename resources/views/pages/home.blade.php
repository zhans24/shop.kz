{{-- resources/views/pages/home.blade.php --}}
@extends('layouts.front')

@php
    $page    = $page    ?? null;   // ← чтобы не было "Undefined variable"
    $homeDto = $homeDto ?? ['exists'=>false,'meta'=>[]];

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
                                    @if(!empty($s['left_url']))
                                        <img class="bg-img" src="{{ $s['left_url'] }}" alt="" loading="lazy" decoding="async">
                                    @endif
                                </div>

                                <div class="hero__text">
                                    @if(!empty($s['title']))
                                        @if($i === 0)
                                            {{-- настоящий главный заголовок страницы --}}
                                            <h1>{{ $s['title'] }}</h1>
                                        @else
                                            {{-- визуально тот же самый тег/стили, но помечаем как декоративный --}}
                                            <h1 aria-hidden="true" role="presentation">{{ $s['title'] }}</h1>
                                        @endif
                                    @endif

                                    @if(!empty($s['text']))
                                        <p>{{ $s['text'] }}</p>
                                    @endif
                                </div>

                                <div class="hero__img">
                                    @if(!empty($s['right_url']))
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
                        {{-- SVG --}}
                        <svg width="40" height="39" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M26 18.7501C26.4142 18.7501 26.75 19.0859 26.75 19.5001C26.75 19.9143 26.4142 20.2501 26 20.2501V19.5001V18.7501ZM14.8571 20.2501C14.4429 20.2501 14.1071 19.9143 14.1071 19.5001C14.1071 19.0859 14.4429 18.7501 14.8571 18.7501V19.5001V20.2501ZM19.4764 13.1129C19.773 12.8237 20.2478 12.8298 20.537 13.1263C20.8262 13.4229 20.8202 13.8977 20.5236 14.1869L20 13.6499L19.4764 13.1129ZM14 19.4999L13.4764 20.0369C13.3316 19.8957 13.25 19.7021 13.25 19.4999C13.25 19.2977 13.3316 19.1041 13.4764 18.9629L14 19.4999ZM20.5236 24.8129C20.8202 25.1021 20.8262 25.5769 20.537 25.8735C20.2478 26.1701 19.773 26.1761 19.4764 25.8869L20 25.3499L20.5236 24.8129ZM26 19.5001V20.2501H14.8571V19.5001V18.7501H26V19.5001ZM20 13.6499L20.5236 14.1869L14.5236 20.0369L14 19.4999L13.4764 18.9629L19.4764 13.1129L20 13.6499ZM14 19.4999L14.5236 18.9629L20.5236 24.8129L20 25.3499L19.4764 25.8869L13.4764 20.0369L14 19.4999Z" fill="white"></path>
                            <path d="M20 0.5C30.7816 0.5 39.5 9.01845 39.5 19.5C39.5 29.9816 30.7816 38.5 20 38.5C9.21844 38.5 0.5 29.9816 0.5 19.5C0.5 9.01845 9.21844 0.5 20 0.5Z" fill="black" fill-opacity="0.01" stroke="#F6F3E4"></path>
                        </svg>
                    </div>
                    <div class="hero-swiper-button hero-swiper-button-next">
                        {{-- SVG --}}
                        <svg width="40" height="39" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 0.5C30.7816 0.5 39.5 9.01845 39.5 19.5C39.5 29.9816 30.7816 38.5 20 38.5C9.21844 38.5 0.5 29.9816 0.5 19.5C0.5 9.01845 9.21844 0.5 20 0.5Z" fill="black" fill-opacity="0.01" stroke="#F6F3E4"></path>
                            <path d="M14 18.7501C13.5858 18.7501 13.25 19.0859 13.25 19.5001C13.25 19.9143 13.5858 20.2501 14 20.2501V19.5001V18.7501ZM25.1429 20.2501C25.5571 20.2501 25.8929 19.9143 25.8929 19.5001C25.8929 19.0859 25.5571 18.7501 25.1429 18.7501V19.5001V20.2501ZM20.5236 13.1129C20.227 12.8237 19.7522 12.8298 19.463 13.1263C19.1738 13.4229 19.1798 13.8977 19.4764 14.1869L20 13.6499L20.5236 13.1129ZM26 19.4999L26.5236 20.0369C26.6684 19.8957 26.75 19.7021 26.75 19.4999C26.75 19.2977 26.6684 19.1041 26.5236 18.9629L26 19.4999ZM19.4764 24.8129C19.1798 25.1021 19.1738 25.5769 19.463 25.8735C19.7522 26.1701 20.227 26.1761 20.5236 25.8869L20 25.3499L19.4764 24.8129ZM14 19.5001V20.2501H25.1429V19.5001V18.7501H14V19.5001ZM20 13.6499L19.4764 14.1869L25.4764 20.0369L26 19.4999L26.5236 18.9629L20.5236 13.1129L20 13.6499ZM26 19.4999L25.4764 18.9629L19.4764 24.8129L20 25.3499L20.5236 25.8869L26.5236 20.0369L26 19.4999Z" fill="white"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <div class="centeres">
        {{-- CALL BTNS --}}
        @php
            // Берём контакты как в футере
            $c = $contacts ?? site_contacts();

            // Телефоны (ожидаем структуру как в футере: ['tel' => 'tel:+7701...', 'raw' => '+7 (701) ...'])
            $phones = collect($c['phones'] ?? []);
            $first  = $phones->first();

            // tel: — приоритетно берём уже подготовленное значение 'tel' (если его формируешь на бэке).
            // Если нет — соберём из 'raw' (оставим только цифры и плюс).
            $telHref = $first['tel']
                ?? (isset($first['raw']) ? 'tel:' . preg_replace('/[^\d\+]+/', '', $first['raw']) : null);

            // WhatsApp — если явно задано в контактах, берём его.
            // Иначе построим ссылку на первый телефон: https://wa.me/<digits>
            $waHref = $c['whatsapp']
                ?? (isset($first['raw']) ? 'https://wa.me/' . preg_replace('/\D+/', '', $first['raw']) : null);
        @endphp
        <div class="call-btns">
            <a class="call" href="{{ $telHref ?: '#!' }}">
                <img src="{{ asset('img/icons/phone.svg') }}" alt="phone">
            </a>
            <a class="call" href="{{ $waHref ?: '#!' }}" target="_blank" rel="noopener">
                <img src="{{ asset('img/icons/whatsapp.svg') }}" alt="whatsapp">
            </a>
        </div>

        {{-- КАТЕГОРИИ (коллекция) --}}
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

        {{-- ХИТЫ --}}
        @if(!empty($hits) && (is_countable($hits) ? count($hits) : $hits->count()))
            <section class="hits">
                <img class="bg-img" src="{{ asset('img/hits-bg.svg') }}" alt="">
                <h2 class="title">Хиты продаж</h2>
                <div class="swiper hits__slider">
                    <div class="swiper-wrapper">
                        @foreach($hits as $p)
                            @php
                                $isArray = is_array($p);
                                $slug = $isArray ? ($p['slug'] ?? '#') : ($p->slug ?? '#');
                                $name = $isArray ? ($p['name'] ?? '') : ($p->name ?? '');
                                $img  = $isArray
                                    ? ($p['img_thumb'] ?? $p['img_large'] ?? null)
                                    : ($p->coverUrl('thumb') ?? $p->coverUrl('large'));
                                $img  = $img ?: asset('img/not.png');
                            @endphp
                            <div class="swiper-slide">
                                <div class="hits__slide">
                                    <a href="{{ route('product.show', $slug) }}" class="hits__card">
                                        <div class="hits__tag hits__tag--hit">ХИТ</div>
                                        <div class="hits__img">
                                            <img src="{{ $img }}" alt="{{ $name }}">
                                        </div>
                                        <h3>{{ $name }}</h3>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="hits__navigation">
                        <div class="swiper-button-prev">
                            {{-- ТВОЙ SVG --}}
                            <svg width="40" height="39" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26 18.7501C26.4142 18.7501 26.75 19.0859 26.75 19.5001C26.75 19.9143 26.4142 20.2501 26 20.2501V19.5001V18.7501ZM14.8571 20.2501C14.4429 20.2501 14.1071 19.9143 14.1071 19.5001C14.1071 19.0859 14.4429 18.7501 14.8571 18.7501V19.5001V20.2501ZM19.4764 13.1129C19.773 12.8237 20.2478 12.8298 20.537 13.1263C20.8262 13.4229 20.8202 13.8977 20.5236 14.1869L20 13.6499L19.4764 13.1129ZM14 19.4999L13.4764 20.0369C13.3316 19.8957 13.25 19.7021 13.25 19.4999C13.25 19.2977 13.3316 19.1041 13.4764 18.9629L14 19.4999ZM20.5236 24.8129C20.8202 25.1021 20.8262 25.5769 20.537 25.8735C20.2478 26.1701 19.773 26.1761 19.4764 25.8869L20 25.3499L20.5236 24.8129ZM26 19.5001V20.2501H14.8571V19.5001V18.7501H26V19.5001ZM20 13.6499L20.5236 14.1869L14.5236 20.0369L14 19.4999L13.4764 18.9629L19.4764 13.1129L20 13.6499ZM14 19.4999L14.5236 18.9629L20.5236 24.8129L20 25.3499L19.4764 25.8869L13.4764 20.0369L14 19.4999Z" fill="white"></path>
                                <path d="M20 0.5C30.7816 0.5 39.5 9.01845 39.5 19.5C39.5 29.9816 30.7816 38.5 20 38.5C9.21844 38.5 0.5 29.9816 0.5 19.5C0.5 9.01845 9.21844 0.5 20 0.5Z" fill="black" fill-opacity="0.01" stroke="#F6F3E4"></path>
                            </svg>
                        </div>
                        <div class="swiper-button-next">
                            {{-- ТВОЙ SVG --}}
                            <svg width="40" height="39" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 0.5C30.7816 0.5 39.5 9.01845 39.5 19.5C39.5 29.9816 30.7816 38.5 20 38.5C9.21844 38.5 0.5 29.9816 0.5 19.5C0.5 9.01845 9.21844 0.5 20 0.5Z" fill="black" fill-opacity="0.01" stroke="#F6F3E4"></path>
                                <path d="M14 18.7501C13.5858 18.7501 13.25 19.0859 13.25 19.5001C13.25 19.9143 13.5858 20.2501 14 20.2501V19.5001V18.7501ZM25.1429 20.2501C25.5571 20.2501 25.8929 19.9143 25.8929 19.5001C25.8929 19.0859 25.5571 18.7501 25.1429 18.7501V19.5001V20.2501ZM20.5236 13.1129C20.227 12.8237 19.7522 12.8298 19.463 13.1263C19.1738 13.4229 19.1798 13.8977 19.4764 14.1869L20 13.6499L20.5236 13.1129ZM26 19.4999L26.5236 20.0369C26.6684 19.8957 26.75 19.7021 26.75 19.4999C26.75 19.2977 26.6684 19.1041 26.5236 18.9629L26 19.4999ZM19.4764 24.8129C19.1798 25.1021 19.1738 25.5769 19.463 25.8735C19.7522 26.1701 20.227 26.1761 20.5236 25.8869L20 25.3499L19.4764 24.8129ZM14 19.5001V20.2501H25.1429V19.5001V18.7501H14V19.5001ZM20 13.6499L19.4764 14.1869L25.4764 20.0369L26 19.4999L26.5236 18.9629L20.5236 13.1129L20 13.6499ZM26 19.4999L25.4764 18.9629L19.4764 24.8129L20 25.3499L20.5236 25.8869L26.5236 20.0369L26 19.4999Z" fill="white"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </section>
        @endif


        {{-- НОВИНКИ (массив) --}}
        @if(!empty($newest))
            <section class="novelty">
                <span>Создаем комфорт</span>
                <div class="novelty__wrapper container">
                    <h2 class="title">Новинки</h2>
                    <div class="novelty__wrapp">
                        @foreach($newest as $p)
                            <a href="{{ route('product.show', $p['slug'] ?? '#') }}" class="novelty__card">
                                <div class="novelty__img">
                                    @if(!empty($p['img_large']))
                                        <img class="bg-img" src="{{ $p['img_large'] }}" alt="{{ $p['name'] }}">
                                    @endif
                                </div>
                                <div class="novelty__text">
                                    <h3>{{ $p['name'] }}</h3>
                                    {{-- SVG --}}
                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="20" cy="20" r="20" transform="matrix(-1 0 0 1 40 0)" fill="#6D031A"></circle>
                                        <path d="M14 19.25C13.5858 19.25 13.25 19.5858 13.25 20C13.25 20.4142 13.5858 20.75 14 20.75V20V19.25ZM25.1429 20.75C25.5571 20.75 25.8929 20.4142 25.8929 20C25.8929 19.5858 25.5571 19.25 25.1429 19.25V20V20.75ZM20.5303 13.4697C20.2374 13.1768 19.7626 13.1768 19.4697 13.4697C19.1768 13.7626 19.1768 14.2374 19.4697 14.5303L20 14L20.5303 13.4697ZM26 20L26.5303 20.5303C26.8232 20.2374 26.8232 19.7626 26.5303 19.4697L26 20ZM19.4697 25.4697C19.1768 25.7626 19.1768 26.2374 19.4697 26.5303C19.7626 26.8232 20.2374 26.8232 20.5303 26.5303L20 26L19.4697 25.4697ZM14 20V20.75H25.1429V20V19.25H14V20ZM20 14L19.4697 14.5303L25.4697 20.5303L26 20L26.5303 19.4697L20.5303 13.4697L20 14ZM26 20L25.4697 19.4697L19.4697 25.4697L20 26L20.5303 26.5303L26.5303 20.5303L26 20Z" fill="white"></path>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <a href="{{ route('categories.index') }}" class="btn-hov">Показать все</a>
                </div>
            </section>
        @endif

        @if(!empty($promos) && (is_countable($promos) ? count($promos) : $promos->count()))

            <section class="stock hits">
                <h2 class="title">Акции и спецпредложения</h2>

                <div class="swiper hits__slider">
                    <div class="swiper-wrapper">
                        @foreach($promos as $p)
                            @php
                                $isArray = is_array($p);
                                $slug = $isArray ? ($p['slug'] ?? '#') : ($p->slug ?? '#');
                                $name = $isArray ? ($p['name'] ?? '') : ($p->name ?? '');
                                $pct  = $isArray ? (int)($p['discount_pct'] ?? 0) : (int)($p->discount_percent ?? 0);
                                $img  = $isArray
                                    ? ($p['img_thumb'] ?? $p['img_large'] ?? null)
                                    : ($p->coverUrl('thumb') ?? $p->coverUrl('large'));
                                $img  = $img ?: asset('img/not.png');
                            @endphp
                            <div class="swiper-slide">
                                <div class="hits__slide">
                                    <a href="{{ route('product.show', $slug) }}" class="hits__card">
                                        @if($pct > 0)
                                            <div class="hits__tag hits__tag--sale">-{{ $pct }}%</div>
                                        @endif
                                        <div class="hits__img">
                                            <img src="{{ $img }}" alt="{{ $name }}">
                                        </div>
                                        <h3>{{ $name }}</h3>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="hits__navigation">
                        <div class="swiper-button-prev">
                            {{-- ТВОЙ SVG --}}
                            <svg width="40" height="39" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26 18.7501C26.4142 18.7501 26.75 19.0859 26.75 19.5001C26.75 19.9143 26.4142 20.2501 26 20.2501V19.5001V18.7501ZM14.8571 20.2501C14.4429 20.2501 14.1071 19.9143 14.1071 19.5001C14.1071 19.0859 14.4429 18.7501 14.8571 18.7501V19.5001V20.2501ZM19.4764 13.1129C19.773 12.8237 20.2478 12.8298 20.537 13.1263C20.8262 13.4229 20.8202 13.8977 20.5236 14.1869L20 13.6499L19.4764 13.1129ZM14 19.4999L13.4764 20.0369C13.3316 19.8957 13.25 19.7021 13.25 19.4999C13.25 19.2977 13.3316 19.1041 13.4764 18.9629L14 19.4999ZM20.5236 24.8129C20.8202 25.1021 20.8262 25.5769 20.537 25.8735C20.2478 26.1701 19.773 26.1761 19.4764 25.8869L20 25.3499L20.5236 24.8129ZM26 19.5001V20.2501H14.8571V19.5001V18.7501H26V19.5001ZM20 13.6499L20.5236 14.1869L14.5236 20.0369L14 19.4999L13.4764 18.9629L19.4764 13.1129L20 13.6499ZM14 19.4999L14.5236 18.9629L20.5236 24.8129L20 25.3499L19.4764 25.8869L13.4764 20.0369L14 19.4999Z" fill="white"></path>
                                <path d="M20 0.5C30.7816 0.5 39.5 9.01845 39.5 19.5C39.5 29.9816 30.7816 38.5 20 38.5C9.21844 38.5 0.5 29.9816 0.5 19.5C0.5 9.01845 9.21844 0.5 20 0.5Z" fill="black" fill-opacity="0.01" stroke="#F6F3E4"></path>
                            </svg>
                        </div>
                        <div class="swiper-button-next">
                            {{-- ТВОЙ SVG --}}
                            <svg width="40" height="39" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 0.5C30.7816 0.5 39.5 9.01845 39.5 19.5C39.5 29.9816 30.7816 38.5 20 38.5C9.21844 38.5 0.5 29.9816 0.5 19.5C0.5 9.01845 9.21844 0.5 20 0.5Z" fill="black" fill-opacity="0.01" stroke="#F6F3E4"></path>
                                <path d="M14 18.7501C13.5858 18.7501 13.25 19.0859 13.25 19.5001C13.25 19.9143 13.5858 20.2501 14 20.2501V19.5001V18.7501ZM25.1429 20.2501C25.5571 20.2501 25.8929 19.9143 25.8929 19.5001C25.8929 19.0859 25.5571 18.7501 25.1429 18.7501V19.5001V20.2501ZM20.5236 13.1129C20.227 12.8237 19.7522 12.8298 19.463 13.1263C19.1738 13.4229 19.1798 13.8977 19.4764 14.1869L20 13.6499L20.5236 13.1129ZM26 19.4999L26.5236 20.0369C26.6684 19.8957 26.75 19.7021 26.75 19.4999C26.75 19.2977 26.6684 19.1041 26.5236 18.9629L26 19.4999ZM19.4764 24.8129C19.1798 25.1021 19.1738 25.5769 19.463 25.8735C19.7522 26.1701 20.227 26.1761 20.5236 25.8869L20 25.3499L19.4764 24.8129ZM14 19.5001V20.2501H25.1429V19.5001V18.7501H14V19.5001ZM20 13.6499L19.4764 14.1869L25.4764 20.0369L26 19.4999L26.5236 18.9629L20.5236 13.1129L20 13.6499ZM26 19.4999L25.4764 18.9629L19.4764 24.8129L20 25.3499L20.5236 25.8869L26.5236 20.0369L26 19.4999Z" fill="white"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- НОВОСТИ (массив) --}}
        {{-- НОВОСТИ (массив) --}}
        @if(!empty($news))
            <section class="news">
                <span>Создаем комфорт</span>
                <div class="novelty__wrapper container">
                    <h2 class="title">Новости</h2>
                    <div class="novelty__wrapp">
                        @foreach($news as $post)
                            @php
                                // поддерживаем и массив, и модель
                                $title    = is_array($post) ? ($post['title'] ?? '') : ($post->title ?? '');
                                $excerpt  = is_array($post) ? ($post['excerpt'] ?? '') : ($post->excerpt ?? '');
                                $slug     = is_array($post) ? ($post['slug'] ?? '#') : ($post->slug ?? '#');
                                $img      = is_array($post) ? ($post['img'] ?? null) : ($post->getFirstMediaUrl('cover','large') ?? $post->getFirstMediaUrl('cover'));
                                // дата-время
                                $dt = null;
                                if (is_array($post)) {
                                    $dt = $post['published_at'] ?? ($post['date'] ?? null); // подхватим что есть
                                    // если это Carbon уже — оставим; если строка — попробуем Carbon::parse()
                                    if (!($dt instanceof \Carbon\Carbon) && !empty($dt)) {
                                        try { $dt = \Carbon\Carbon::parse($dt); } catch (\Exception $e) { $dt = null; }
                                    }
                                } else {
                                    $dt = optional($post->published_at);
                                }
                            @endphp

                            <a href="{{ route('posts.index', $slug) }}" class="novelty__card news__card">
                                <div class="novelty__img">
                                    @if(!empty($img))
                                        <img class="bg-img" src="{{ $img }}" alt="{{ $title }}">
                                    @endif
                                </div>

                                <h3>{{ $title }}</h3>

                                @if(!empty($excerpt))
                                    <p>{{ $excerpt }}</p>
                                @endif

                                <div class="novelty__text">
                                    <span>{{ $dt ? $dt->format('d.m.Y') : '' }}</span>
                                    <span>
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="8" cy="8" r="8" transform="matrix(-1 0 0 1 17 1)" stroke="#605E5D"/>
                                    <path d="M9 6V10H12.5" stroke="#605E5D"/>
                                </svg>
                                {{ $dt ? $dt->format('H:i') : '' }}
                            </span>
                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="20" cy="20" r="20" transform="matrix(-1 0 0 1 40 0)" fill="#6D031A"></circle>
                                        <path d="M14 19.25C13.5858 19.25 13.25 19.5858 13.25 20C13.25 20.4142 13.5858 20.75 14 20.75V20V19.25ZM25.1429 20.75C25.5571 20.75 25.8929 20.4142 25.8929 20C25.8929 19.5858 25.5571 19.25 25.1429 19.25V20V20.75ZM20.5303 13.4697C20.2374 13.1768 19.7626 13.1768 19.4697 13.4697C19.1768 13.7626 19.1768 14.2374 19.4697 14.5303L20 14L20.5303 13.4697ZM26 20L26.5303 20.5303C26.8232 20.2374 26.8232 19.7626 26.5303 19.4697L26 20ZM19.4697 25.4697C19.1768 25.7626 19.1768 26.2374 19.4697 26.5303C19.7626 26.8232 20.2374 26.8232 20.5303 26.5303L20 26L19.4697 25.4697ZM14 20V20.75H25.1429V20V19.25H14V20ZM20 14L19.4697 14.5303L25.4697 20.5303L26 20L26.5303 19.4697L20.5303 13.4697L20 14ZM26 20L25.4697 19.4697L19.4697 25.4697L20 26L20.5303 26.5303L26.5303 20.5303L26 20Z" fill="white"></path>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection
