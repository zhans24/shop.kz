@php
    $c = $contacts ?? site_contacts();
    $phones = collect($c['phones'] ?? [])->take(2)->values();
    $email  = $c['email'] ?? null;
    $address = $c['address'] ?? null;
@endphp

<footer class="footer">
    <div class="footer__top container">
        <div class="footer__item">
            <a href="{{ route('front.home') }}"
                rel="noopener noreferrer">
                <img src="{{ asset('img/logo.svg') }}" alt="">
            </a>
            <p>{{ $c['company_text'] ?? ' ' }}</p>
        </div>

        <ul>
            <p>Карта сайта</p>
            <li>
                <a href="{{ route('categories.index') }}"
                   rel="noopener noreferrer">
                    Каталог товаров
                </a>
            </li>
            <li>
                <a href="{{ route('front.about') }}"
                   rel="noopener noreferrer">
                    О компании
                </a>
            </li>
            <li>
                <a href="{{ route('front.delivery') }}"
                    rel="noopener noreferrer">
                    Доставка и оплата
                </a>
            </li>
            <li>
                <a href="{{ url('/news') }}"
                    rel="noopener noreferrer">
                    Новости и акции
                </a>
            </li>
            <li>
                <a href="{{ route('front.contacts') }}"
                    rel="noopener noreferrer">
                    Контакты
                </a>
            </li>
        </ul>

        <ul>
            <p>Контакты</p>

            {{-- Телефоны (ровно 2 строки) --}}
            @for($i=0; $i<2; $i++)
                @php
                    $ph  = $phones->get($i);
                    $tel = $ph['tel'] ?? null;
                    $raw = $ph['raw'] ?? null;
                @endphp
                <li>
                    <!-- SVG из верстки -->
                    <svg width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="9.07722" cy="6.76912" r="2.30769" stroke="black" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M1 4.46154H2.15385V3.30769C2.15385 2.03319 3.18704 1 4.46154 1H13.6923C14.9668 1 16 2.03319 16 3.30769V14.8462C16 16.1207 14.9668 17.1538 13.6923 17.1538H4.46154C3.18704 17.1538 2.15385 16.1207 2.15385 14.8462V13.6923H1"
                              stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M1 9.07692H2.15385" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M1 6.76931H2.15385" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M1 11.3845H2.15385" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M12.5383 12.5387V11.3848C12.5383 10.1103 11.5051 9.07715 10.2306 9.07715H7.92293C6.64842 9.07715 5.61523 10.1103 5.61523 11.3848V12.5387C5.61523 13.1759 6.13183 13.6925 6.76908 13.6925H11.3845C12.0217 13.6925 12.5383 13.1759 12.5383 12.5387Z"
                              stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                    @if($tel && $raw)
                        <a href="{{ $tel }}"
                           target="_blank" rel="noopener noreferrer">
                            {{ $raw }}
                        </a>
                    @else
                        <a href="#!"
                           target="_blank" rel="noopener noreferrer">
                            Не установлено
                        </a>
                    @endif
                </li>
            @endfor

            {{-- Email --}}
            <li>
                <!-- SVG из верстки -->
                <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M1 3.16667V11.8333C1 13.03 2.02335 14 3.28571 14H14.7143C15.9767 14 17 13.03 17 11.8333V3.16667C17 1.97005 15.9767 1 14.7143 1H3.28571C2.02335 1 1 1.97005 1 3.16667Z"
                          stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M3.28613 4.42871L9.00042 7.85728L14.7147 4.42871" stroke="black" stroke-width="1.5"
                          stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                @if($email)
                    <a href="mailto:{{ $email }}"
                       target="_blank" rel="noopener noreferrer">
                        {{ $email }}
                    </a>
                @else
                    <a href="#!"
                       target="_blank" rel="noopener noreferrer">
                        Не установлено
                    </a>
                @endif
            </li>

            {{-- Адрес --}}
            <li>
                <!-- SVG из верстки -->
                <svg width="16" height="21" viewBox="0 0 16 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M8 19.7142L8.7363 18.8837C9.57146 17.9281 10.3231 17.019 10.9912 16.1564L11.5435 15.4284C13.8478 12.3234 15 9.86022 15 8.03902C15 4.15148 11.866 1 8 1C4.13401 1 1 4.15148 1 8.03902C1 9.86022 2.15218 12.3234 4.45654 15.4284L5.00877 16.1564C5.86779 17.2654 6.86486 18.4514 8 19.7142Z"
                          stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <circle cx="7.99967" cy="8.00016" r="2.91667" stroke="black" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                @if($address)
                    <a href="#!"
                       target="_blank" rel="noopener noreferrer">
                        {!! nl2br(e($address)) !!}
                    </a>
                @else
                    <a href="#!"
                       target="_blank" rel="noopener noreferrer">
                        Не установлено
                    </a>
                @endif
            </li>
        </ul>

        <ul>
            <p>Социальные сети</p>
            <div class="socials">
                <li>
                    <a href="{{ $c['whatsapp'] ?? '#!' }}"
                       target="_blank" rel="noopener noreferrer">
                        <!-- WhatsApp -->
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="20" fill="#6D031A"></circle>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M28.5033 11.4877C26.2472 9.23984 23.2467 8.00126 20.0501 8C13.4635 8 8.10283 13.3346 8.1002 19.8918C8.09932 21.9878 8.6496 24.0338 9.69528 25.8373L8 32L14.3348 30.3463C16.0802 31.2937 18.0453 31.793 20.0453 31.7938H20.0502C26.6362 31.7938 31.9973 26.4585 32 19.9013C32.0013 16.7236 30.7595 13.7356 28.5033 11.4877ZM20.0502 29.7852H20.0462C18.264 29.7845 16.5159 29.308 14.991 28.4074L14.6283 28.1932L10.8692 29.1746L11.8726 25.527L11.6364 25.153C10.6422 23.5792 10.117 21.7602 10.1178 19.8925C10.12 14.4425 14.5756 10.0086 20.0542 10.0086C22.707 10.0096 25.2008 11.0391 27.0761 12.9075C28.9513 14.7758 29.9834 17.2593 29.9824 19.9005C29.9801 25.3509 25.5246 29.7852 20.0502 29.7852ZM23.458 21.4154C23.7317 21.5145 25.1997 22.2334 25.4983 22.3822C25.5565 22.4112 25.611 22.4374 25.6615 22.4617C25.8698 22.5618 26.0105 22.6295 26.0705 22.7293C26.1452 22.8532 26.1452 23.4482 25.8964 24.1423C25.6475 24.8364 24.4546 25.47 23.881 25.5553C23.3665 25.6318 22.7156 25.6637 22.0003 25.4376C21.5666 25.3006 21.0105 25.1178 20.2981 24.8116C17.4992 23.6089 15.6077 20.9092 15.2499 20.3985C15.2248 20.3627 15.2072 20.3376 15.1974 20.3246L15.1949 20.3213C15.0366 20.1111 13.9782 18.7055 13.9782 17.2507C13.9782 15.8819 14.6538 15.1644 14.9648 14.8342C14.9861 14.8116 15.0057 14.7908 15.0232 14.7717C15.2969 14.4743 15.6204 14.3999 15.8195 14.3999C16.0185 14.3999 16.2177 14.4017 16.3917 14.4103C16.4131 14.4114 16.4354 14.4113 16.4585 14.4111C16.6325 14.4101 16.8495 14.4087 17.0635 14.9204C17.1461 15.118 17.2672 15.4112 17.3947 15.7203C17.6513 16.3419 17.9343 17.0274 17.9841 17.1268C18.0588 17.2755 18.1085 17.449 18.009 17.6474C17.9941 17.677 17.9804 17.7049 17.9672 17.7316C17.8924 17.8838 17.8374 17.9955 17.7104 18.1431C17.6607 18.201 17.6092 18.2633 17.5578 18.3257C17.4549 18.4503 17.3521 18.575 17.2626 18.6637C17.1131 18.8119 16.9574 18.9727 17.1316 19.2702C17.3058 19.5677 17.9051 20.5409 18.7927 21.3287C19.7468 22.1757 20.5761 22.5337 20.9964 22.7152C21.0785 22.7506 21.145 22.7793 21.1938 22.8036C21.4924 22.9524 21.6666 22.9276 21.8407 22.7292C22.0149 22.5309 22.5872 21.8616 22.7862 21.5641C22.9853 21.2666 23.1843 21.3162 23.458 21.4154Z" fill="white"></path>
                        </svg>
                    </a>
                </li>

                <li>
                    <a href="{{ $c['youtube'] ?? '#!' }}"
                       target="_blank" rel="noopener noreferrer">
                        <!-- YouTube -->
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="20" fill="#6D031A"></circle>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M29.5047 12.4881C30.4793 12.7575 31.2462 13.5187 31.5177 14.5032H31.5184L31.4893 14.3186C31.8244 16.0991 32 17.9349 32 19.8116C32 19.8463 31.9998 19.8811 31.9996 19.9143C31.9994 19.9413 31.9993 19.9673 31.9992 19.9915C32 20.0571 32 20.1235 32 20.1899C32 22.0658 31.8252 23.9024 31.5192 25.4983L31.5146 25.518C31.2477 26.4828 30.4809 27.244 29.4855 27.518L29.8183 27.4847C27.037 27.8244 24.2042 28 21.3293 28C21.0379 28 20.7465 27.9977 20.4996 27.9947L20.5441 27.9939C20.2527 27.9977 19.9613 27.9992 19.6699 27.9992C16.7958 27.9992 13.9638 27.8237 11.5153 27.5165L11.4945 27.5119C10.5199 27.2425 9.75304 26.4813 9.48158 25.4968L9.51072 25.6814C9.17561 23.9009 9 22.0651 9 20.1884C9 20.1537 9.00021 20.1189 9.00041 20.0857C9.00057 20.059 9.00072 20.0332 9.00076 20.0092C9 19.9436 9 19.8772 9 19.8108C9 17.9342 9.17484 16.0976 9.48158 14.5017L9.48618 14.482C9.75228 13.5172 10.5191 12.7567 11.5137 12.482L11.1809 12.5153C13.9623 12.1756 16.7958 12 19.6699 12C19.9621 12 20.2527 12.0023 20.4996 12.0053L20.4551 12.0061C20.7458 12.0023 21.0372 12.0008 21.3293 12.0008C24.2035 12.0008 27.0354 12.1763 29.484 12.4835L29.5047 12.4881ZM18.1469 16.6273V23.375L24.1582 20.0015L18.1469 16.6273Z" fill="white"></path>
                        </svg>
                    </a>
                </li>

                <li>
                    <a href="{{ $c['facebook'] ?? '#!' }}"
                       target="_blank" rel="noopener noreferrer">
                        <!-- Facebook -->
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="20" fill="#6D031A"></circle>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M25 11.5C25 11.224 24.775 11 24.5 11H22C19.238 11 17 13.015 17 15.5V18.2H14.5C14.224 18.2 14 18.424 14 18.7V21.3C14 21.576 14.224 21.8 14.5 21.8H17V28.5C17 28.776 17.224 29 17.5 29H20.5C20.775 29 21 28.776 21 28.5V21.8H23.619C23.844 21.8 24.041 21.65 24.102 21.434L24.823 18.834C24.912 18.516 24.672 18.2 24.342 18.2H21V15.5C21 15.003 21.447 14.6 22 14.6H24.5C24.775 14.6 25 14.376 25 14.1V11.5Z" fill="white"></path>
                        </svg>
                    </a>
                </li>

                <li>
                    <a href="{{ $c['telegram'] ?? '#!' }}"
                       target="_blank" rel="noopener noreferrer">
                        <!-- Telegram -->
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="20" fill="#6D031A"></circle>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.06931 18.9154L28.5297 11.1532C29.4084 10.6604 30.287 11.3996 29.9105 12.7549L26.3958 28.8955C26.1448 30.0044 25.5171 30.2508 24.5129 29.758L19.2409 25.9384L16.7305 28.4026C16.4794 28.6491 16.2284 28.8955 15.7263 28.8955C15.2505 28.8955 15.2075 28.7539 15.1052 28.4171C15.0726 28.3095 15.0339 28.1821 14.9731 28.033L12.9647 22.1189L7.81826 20.5172C6.68855 20.2708 6.68855 19.4083 8.06931 18.9154ZM16.1028 23.5974L25.8937 14.8495C26.2703 14.4799 25.7682 14.2335 25.2661 14.6031L13.7179 21.7493L15.7263 27.6634L16.1028 23.5974Z" fill="white"></path>
                        </svg>
                    </a>
                </li>
            </div>
        </ul>
    </div>

    <div class="footer__bot">
        <div class="footer__info container">
            <a href="#!"
               target="_blank" rel="noopener noreferrer">
                @tf-shop.kz
            </a>

            <a href="{{ route('front.privacy') }}"
               target="_blank" rel="noopener noreferrer">
                Политика конфиденциальности
            </a>

            <a href="https://astanacreative.kz/"
               target="_blank" rel="noopener noreferrer">
                Разработано Astana Creative
            </a>
        </div>
    </div>
</footer>
