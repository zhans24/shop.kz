<footer class="footer">
    <div class="footer__top container">
        <div class="footer__item">
            <a href="{{ route('front.home') }}">
                <img src="{{ asset('img/logo.svg') }}" alt="logo">
            </a>
            <p>Lorem Ipsum is simply text of the printing and industry the printing and .</p>
        </div>

        <ul>
            <p>Карта сайта</p>
            <li>
                <a href="{{ route('categories.index') }}">Каталог товаров</a>
            </li>
            <li>
                <a href="{{ Route::has('front.about') ? route('front.about') : url('/about') }}">О компании</a>
            </li>
            <li>
                <a href="{{ Route::has('front.delivery') ? route('front.delivery') : url('/delivery') }}">Доставка и оплата</a>
            </li>
            <li>
                <a href="{{ url('/news') }}">Новости и акции</a>
            </li>
            <li>
                <a href="{{ url('/contacts') }}">Контакты</a>
            </li>
        </ul>

        <ul>
            <p>Контакты</p>
            <li>
                <img src="{{ asset('img/icons/phone.svg') }}" alt="" width="18" height="18">
                <a href="tel:77478803467">+ 747 880 34 67</a>
            </li>
            <li>
                <img src="{{ asset('img/icons/filled.svg') }}" alt="" width="18" height="18">
                <a href="mailto:tf-shop.kz@gmail.com">tf-shop.kz@gmail.com</a>
            </li>
            <li>
                <img src="{{ asset('img/icons/phone.svg') }}" alt="" width="18" height="18">
                <a href="#">Адрес: Алматы, Абая Алтынсарина 416</a>
            </li>
        </ul>

        <ul>
            <p>Социальные сети</p>
            <div class="socials">
                <li><a href="#"><img src="{{ asset('img/logo-light.svg') }}" alt="" width="40" height="40"></a></li>
                <li><a href="#"><img src="{{ asset('img/logo-light.svg') }}" alt="" width="40" height="40"></a></li>
                <li><a href="#"><img src="{{ asset('img/logo-light.svg') }}" alt="" width="40" height="40"></a></li>
                <li><a href="#"><img src="{{ asset('img/logo-light.svg') }}" alt="" width="40" height="40"></a></li>
            </div>
        </ul>
    </div>

    <div class="footer__bot">
        <div class="footer__info container">
            <a href="#!">@tf-shop.kz</a>
            <a href="{{ url('/privacy') }}">Политика конфиденциальности</a>
            <a href="#!">Разработано Astana Creative</a>
        </div>
    </div>
</footer>
