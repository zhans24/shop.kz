{{-- resources/views/partials/header.blade.php --}}
<header class="header" id="mainHeader">
    <div class="header__space">
        <div class="container">
            <div class="header__location container" id="location">
                <!-- SVG из верстки -->
                <svg width="14" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M7 17.0408L7.63112 16.3289C8.34697 15.5098 8.99123 14.7306 9.56392 13.9912L10.0373 13.3672C12.0124 10.7057 13 8.59447 13 7.03345C13 3.70127 10.3137 1 7 1C3.68629 1 1 3.70127 1 7.03345C1 8.59447 1.98758 10.7057 3.96275 13.3672L4.43608 13.9912C5.17239 14.9418 6.02703 15.9583 7 17.0408Z"
                          stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                    <circle cx="7" cy="7" r="2.5" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                <span class="location__city">{{ $currentCity['name'] ?? 'Алматы' }}</span>

                <ul id="locationDropdown">
                    @forelse($headerCities as $city)
                        @continue(($currentCity['slug'] ?? null) === ($city['slug'] ?? null))
                        <li data-city="{{ $city['slug'] }}">{{ $city['name'] }}</li>
                    @empty
                        <li data-city="almaty">Алматы</li>
                        <li data-city="astana">Астана</li>
                    @endforelse
                </ul>
            </div>

            @php
                $c = $contacts ?? site_contacts();
                $phones = collect($c['phones'] ?? [])->take(2)->values();
            @endphp

            <div class="header__phones">
                @for($i=0; $i<2; $i++)
                    @php
                        $ph  = $phones->get($i);
                        $tel = $ph['tel'] ?? null;
                        $raw = $ph['raw'] ?? null;
                    @endphp

                    @if($tel && $raw)
                        <a href="{{ $tel }}">{{ $raw }}</a>
                    @else
                        <a href="#!">Не установлено</a>
                    @endif
                @endfor
            </div>
        </div>
    </div>

    <div class="header__sticky container {{ $headerModifier ?? '' }}">
        <a class="header__logo" href="{{ route('front.home') }}">
            <img
                src="{{ asset(($headerTheme ?? 'light') === 'dark' ? 'img/logo-light.svg' : 'img/logo.svg') }}"
                alt="logo">
        </a>

        <div class="burger" id="burger">
            <div class="burger-dot burger-dot--line burger-dot--left-top"></div>
            <div class="burger-dot"></div>
            <div class="burger-dot burger-dot--line burger-dot--right-top"></div>
            <div class="burger-dot"></div>
            <div class="burger-dot"></div>
            <div class="burger-dot"></div>
            <div class="burger-dot burger-dot--line burger-dot--left-bottom"></div>
            <div class="burger-dot"></div>
            <div class="burger-dot burger-dot--line burger-dot--right-bottom"></div>
        </div>

        <nav class="header__nav">
            <ul>
                <li><a class="{{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">Каталог товаров</a></li>
                <li><a class="{{ request()->routeIs('front.about') ? 'active' : '' }}" href="{{ route('front.about') }}">О компании</a></li>
                <li><a class="{{ request()->routeIs('front.delivery') ? 'active' : '' }}" href="{{ route('front.delivery') }}">Доставка и оплата</a></li>
                <li><a href="{{ url('/news') }}">Новости и акции</a></li>
                <li><a class="{{ request()->routeIs('front.contacts') ? 'active' : '' }}" href="{{ route('front.contacts') }}">Контакты</a></li>
            </ul>
        </nav>

        <div class="search-wrap">
            <input type="search" id="search" placeholder="Поиск">
            <div id="searchSuggest" class="search-suggest" aria-live="polite"></div>
        </div>

        <a class="basket-link" href="{{ route('cart.index') }}">
            <!-- SVG из верстки -->
            <svg width="21" height="17" viewBox="0 0 21 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M4.16667 3.53333H20L17.9916 10.5626C17.6809 11.6502 16.6869 12.4 15.5558 12.4H7.06744C5.77658 12.4 4.69215 11.4294 4.5496 10.1464L3.53333 1H1"
                    stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                <circle cx="6.70026" cy="15.5667" r="1.26667" fill="white" />
                <circle cx="16.834" cy="15.5667" r="1.26667" fill="white" />
            </svg>
        </a>

        {{-- Блок аутентификации Breeze --}}
        <div class="header__auth" style="display:flex; gap:.5rem; align-items:center;">
            @auth
                <a href="{{ route('profile.edit') }}" class="header__profile" aria-label="Профиль"
                   style="display:inline-flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:9999px; background:#1E1512;">
                    {{-- SVG иконка профиля --}}
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5Z" fill="white"/>
                        <path d="M21 22a9 9 0 0 0-18 0" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </a>
            @endauth


            @guest
                <a href="{{ route('login') }}" class="btn-hov">Войти</a>
            @endguest
        </div>

    </div>
</header>
