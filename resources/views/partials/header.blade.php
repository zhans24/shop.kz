<header class="header">
    <div class="header__space">
        <div class="container">
            <div class="header__location container" id="location">
                {{-- ... иконка как была ... --}}
                <span class="location__city">Алматы</span>
                <ul id="locationDropdown">
                    <li data-city="Astana">Астана</li>
                </ul>
            </div>
            <div class="header__phones">
                <a href="tel:77471234567">+7 747 123 45 67</a>
                <a href="tel:77471234567">+7 747 123 45 67</a>
            </div>
        </div>
    </div>

    <div class="header__sticky container">
        <a class="header__logo" href="{{ route('front.home') }}">
            <img src="{{ asset('img/logo.svg') }}" alt="logo">
        </a>

        <div class="burger" id="burger">
            {{-- ... бургер как был ... --}}
        </div>

        <nav class="header__nav">
            <ul>
                <li><a href="{{ route('categories.index') }}">Каталог товаров</a></li>
                <li><a href="{{ Route::has('front.about') ? route('front.about') : url('/about') }}">О компании</a></li>
                <li><a href="{{ Route::has('front.delivery') ? route('front.delivery') : url('/delivery') }}">Доставка и оплата</a></li>
                <li><a href="{{ url('/news') }}">Новости и акции</a></li>
                <li><a href="{{ url('/contacts') }}">Контакты</a></li>
            </ul>
        </nav>

        <input type="search" id="search" placeholder="Поиск">

        <a class="basket-link" href="{{ Route::has('cart.index') ? route('cart.index') : url('/cart') }}">
            <svg width="21" height="17" viewBox="0 0 21 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.16667 3.53333H20L17.9916 10.5626C17.6809 11.6502 16.6869 12.4 15.5558 12.4H7.06744C5.77658 12.4 4.69215 11.4294 4.5496 10.1464L3.53333 1H1" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                <circle cx="6.70026" cy="15.5667" r="1.26667" fill="white"></circle>
                <circle cx="16.834" cy="15.5667" r="1.26667" fill="white"></circle>
            </svg>        </a>

        <a href="{{ Route::has('login') ? route('login') : '#' }}" class="btn-hov">Войти</a>
    </div>
</header>
