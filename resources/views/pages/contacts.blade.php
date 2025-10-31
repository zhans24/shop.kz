@extends('layouts.front')

@php
    $title = 'Контакты';
    $c = $contacts ?? site_contacts();
    $phones = collect($c['phones'] ?? [])->take(2)->values();
    $email  = $c['email'] ?? null;
    $address = $c['address'] ?? null;
@endphp

@section('title', $title)

@section('content')
    <main class="pages">
        <div class="container">
            <nav class="breadcrumbs" aria-label="Хлебные крошки">
                <ol>
                    <li>
                        <a href="{{ route('front.home') }}"
                           target="_blank" rel="noopener noreferrer">
                            Главная
                        </a>
                    </li>
                    <li aria-current="page">{{ $title }}</li>
                </ol>
            </nav>
        </div>

        <div class="centeres">
            <section class="contacts-page">
                <span class="decor-text">Создаем комфорт</span>
            </section>

            <section class="contacts">
                <div class="contacts__left">
                    <h2 class="contacts-title">{{ $title }}</h2>

                    <ul class="contacts-list">
                        {{-- Телефоны: ровно 2 элемента (или "Не установлено") --}}
                        @for($i=0; $i<2; $i++)
                            @php
                                $ph  = $phones->get($i);
                                $tel = $ph['tel'] ?? null;
                                $raw = $ph['raw'] ?? null;
                            @endphp
                            <li class="contacts-item">
                                <a href="{{ $tel ?: '#!' }}"
                                   target="_blank" rel="noopener noreferrer">
                                    <div class="contacts-icon">
                                        <!-- SVG из верстки -->
                                        <svg width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="8.82722" cy="6.51924" r="2.30769" stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            <path
                                                d="M0.75 4.21154H1.90385V3.05769C1.90385 1.78319 2.93704 0.75 4.21154 0.75H13.4423C14.7168 0.75 15.75 1.78319 15.75 3.05769V14.5962C15.75 15.8707 14.7168 16.9038 13.4423 16.9038H4.21154C2.93704 16.9038 1.90385 15.8707 1.90385 14.5962V13.4423H0.75"
                                                stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M0.75 8.82692H1.90385" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M0.75 6.51918H1.90385" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M0.75 11.1346H1.90385" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M12.2883 12.2884V11.1346C12.2883 9.86009 11.2551 8.8269 9.98062 8.8269H7.67293C6.39842 8.8269 5.36523 9.86009 5.36523 11.1346V12.2884C5.36523 12.9257 5.88183 13.4423 6.51908 13.4423H11.1345C11.7717 13.4423 12.2883 12.9257 12.2883 12.2884Z"
                                                  stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                    <p class="contacts-tex">{{ $raw ?: 'Не установлено' }}</p>
                                </a>
                            </li>
                        @endfor

                        {{-- Email --}}
                        <li class="contacts-item">
                            <a href="{{ $email ? ('mailto:'.$email) : '#!' }}"
                               target="_blank" rel="noopener noreferrer">
                                <div class="contacts-icon">
                                    <!-- SVG из верстки -->
                                    <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M0.75 2.91667V11.5833C0.75 12.78 1.77335 13.75 3.03571 13.75H14.4643C15.7267 13.75 16.75 12.78 16.75 11.5833V2.91667C16.75 1.72005 15.7267 0.75 14.4643 0.75H3.03571C1.77335 0.75 0.75 1.72005 0.75 2.91667Z"
                                              stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M3.03516 4.17859L8.74944 7.60716L14.4637 4.17859" stroke="black" stroke-width="1.5" stroke-linecap="round"
                                              stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <p class="contacts-tex">{{ $email ?: 'Не установлено' }}</p>
                            </a>
                        </li>

                        {{-- Адрес --}}
                        <li class="contacts-item">
                            <a href="#!"
                               target="_blank" rel="noopener noreferrer">
                                <div class="contacts-icon">
                                    <!-- SVG из верстки -->
                                    <svg width="16" height="21" viewBox="0 0 16 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M7.75 19.4642L8.4863 18.6337C9.32146 17.6781 10.0731 16.769 10.7412 15.9064L11.2935 15.1784C13.5978 12.0734 14.75 9.61022 14.75 7.78902C14.75 3.90148 11.616 0.75 7.75 0.75C3.88401 0.75 0.75 3.90148 0.75 7.78902C0.75 9.61022 1.90218 12.0734 4.20654 15.1784L4.75877 15.9064C5.61779 17.0154 6.61486 18.2014 7.75 19.4642Z"
                                              stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <circle cx="7.75065" cy="7.74998" r="2.91667" stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <p class="contacts-tex">{!! $address ? nl2br(e($address)) : 'Не установлено' !!}</p>
                            </a>
                        </li>
                    </ul>

                    {{-- соцсети --}}
                    <div class="contacts-socials">
                        {{-- WhatsApp --}}
                        <li>
                            <a href="{{ $c['whatsapp'] ?? '#!' }}"
                               target="_blank" rel="noopener noreferrer">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="20" cy="20" r="20" fill="#6D031A"></circle>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M28.5033 11.4877C26.2472 9.23984 23.2467 8.00126 20.0501 8C13.4635 8 8.10283 13.3346 8.1002 19.8918C8.09932 21.9878 8.6496 24.0338 9.69528 25.8373L8 32L14.3348 30.3463C16.0802 31.2937 18.0453 31.793 20.0453 31.7938H20.0502C26.6362 31.7938 31.9973 26.4585 32 19.9013C32.0013 16.7236 30.7595 13.7356 28.5033 11.4877ZM20.0502 29.7852H20.0462C18.264 29.7845 16.5159 29.308 14.991 28.4074L14.6283 28.1932L10.8692 29.1746L11.8726 25.527L11.6364 25.153C10.6422 23.5792 10.117 21.7602 10.1178 19.8925C10.12 14.4425 14.5756 10.0086 20.0542 10.0086C22.707 10.0096 25.2008 11.0391 27.0761 12.9075C28.9513 14.7758 29.9834 17.2593 29.9824 19.9005C29.9801 25.3509 25.5246 29.7852 20.0502 29.7852ZM23.458 21.4154C23.7317 21.5145 25.1997 22.2334 25.4983 22.3822C25.5565 22.4112 25.611 22.4374 25.6615 22.4617C25.8698 22.5618 26.0105 22.6295 26.0705 22.7293C26.1452 22.8532 26.1452 23.4482 25.8964 24.1423C25.6475 24.8364 24.4546 25.47 23.881 25.5553C23.3665 25.6318 22.7156 25.6637 22.0003 25.4376C21.5666 25.3006 21.0105 25.1178 20.2981 24.8116C17.4992 23.6089 15.6077 20.9092 15.2499 20.3985C15.2248 20.3627 15.2072 20.3376 15.1974 20.3246L15.1949 20.3213C15.0366 20.1111 13.9782 18.7055 13.9782 17.2507C13.9782 15.8819 14.6538 15.1644 14.9648 14.8342C14.9861 14.8116 15.0057 14.7908 15.0232 14.7717C15.2969 14.4743 15.6204 14.3999 15.8195 14.3999C16.0185 14.3999 16.2177 14.4017 16.3917 14.4103C16.4131 14.4114 16.4354 14.4113 16.4585 14.4111C16.6325 14.4101 16.8495 14.4087 17.0635 14.9204C17.1461 15.118 17.2672 15.4112 17.3947 15.7203C17.6513 16.3419 17.9343 17.0274 17.9841 17.1268C18.0588 17.2755 18.1085 17.449 18.009 17.6474C17.9941 17.677 17.9804 17.7049 17.9672 17.7316C17.8924 17.8838 17.8374 17.9955 17.7104 18.1431C17.6607 18.201 17.6092 18.2633 17.5578 18.3257C17.4549 18.4503 17.3521 18.575 17.2626 18.6637C17.1131 18.8119 16.9574 18.9727 17.1316 19.2702C17.3058 19.5677 17.9051 20.5409 18.7927 21.3287C19.7468 22.1757 20.5761 22.5337 20.9964 22.7152C21.0785 22.7506 21.145 22.7793 21.1938 22.8036C21.4924 22.9524 21.6666 22.9276 21.8407 22.7292C22.0149 22.5309 22.5872 21.8616 22.7862 21.5641C22.9853 21.2666 23.1843 21.3162 23.458 21.4154Z" fill="white"></path>
                                </svg>
                            </a>
                        </li>

                        {{-- YouTube --}}
                        <li>
                            <a href="{{ $c['youtube'] ?? '#!' }}"
                               target="_blank" rel="noopener noreferrer">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="20" cy="20" r="20" fill="#6D031A"></circle>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M29.5047 12.4881C30.4793 12.7575 31.2462 13.5187 31.5177 14.5032H31.5184L31.4893 14.3186C31.8244 16.0991 32 17.9349 32 19.8116C32 19.8463 31.9998 19.8811 31.9996 19.9143C31.9994 19.9413 31.9993 19.9673 31.9992 19.9915C32 20.0571 32 20.1235 32 20.1899C32 22.0658 31.8252 23.9024 31.5192 25.4983L31.5146 25.518C31.2477 26.4828 30.4809 27.244 29.4855 27.518L29.8183 27.4847C27.037 27.8244 24.2042 28 21.3293 28C21.0379 28 20.7465 27.9977 20.4996 27.9947L20.5441 27.9939C20.2527 27.9977 19.9613 27.9992 19.6699 27.9992C16.7958 27.9992 13.9638 27.8237 11.5153 27.5165L11.4945 27.5119C10.5199 27.2425 9.75304 26.4813 9.48158 25.4968L9.51072 25.6814C9.17561 23.9009 9 22.0651 9 20.1884C9 20.1537 9.00021 20.1189 9.00041 20.0857C9.00057 20.059 9.00072 20.0332 9.00076 20.0092C9 19.9436 9 19.8772 9 19.8108C9 17.9342 9.17484 16.0976 9.48158 14.5017L9.48618 14.482C9.75228 13.5172 10.5191 12.7567 11.5137 12.482L11.1809 12.5153C13.9623 12.1756 16.7958 12 19.6699 12C19.9621 12 20.2527 12.0023 20.4996 12.0053L20.4551 12.0061C20.7458 12.0023 21.0372 12.0008 21.3293 12.0008C24.2035 12.0008 27.0354 12.1763 29.484 12.4835L29.5047 12.4881ZM18.1469 16.6273V23.375L24.1582 20.0015L18.1469 16.6273Z" fill="white"></path>
                                </svg>
                            </a>
                        </li>

                        {{-- Facebook --}}
                        <li>
                            <a href="{{ $c['facebook'] ?? '#!' }}"
                               target="_blank" rel="noopener noreferrer">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="20" cy="20" r="20" fill="#6D031A"></circle>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M25 11.5C25 11.224 24.775 11 24.5 11H22C19.238 11 17 13.015 17 15.5V18.2H14.5C14.224 18.2 14 18.424 14 18.7V21.3C14 21.576 14.224 21.8 14.5 21.8H17V28.5C17 28.776 17.224 29 17.5 29H20.5C20.775 29 21 28.776 21 28.5V21.8H23.619C23.844 21.8 24.041 21.65 24.102 21.434L24.823 18.834C24.912 18.516 24.672 18.2 24.342 18.2H21V15.5C21 15.003 21.447 14.6 22 14.6H24.5C24.775 14.6 25 14.376 25 14.1V11.5Z" fill="white"></path>
                                </svg>
                            </a>
                        </li>

                        {{-- Telegram --}}
                        <li>
                            <a href="{{ $c['telegram'] ?? '#!' }}"
                               target="_blank" rel="noopener noreferrer">
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="20" cy="20" r="20" fill="#6D031A"></circle>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.06931 18.9154L28.5297 11.1532C29.4084 10.6604 30.287 11.3996 29.9105 12.7549L26.3958 28.8955C26.1448 30.0044 25.5171 30.2508 24.5129 29.758L19.2409 25.9384L16.7305 28.4026C16.4794 28.6491 16.2284 28.8955 15.7263 28.8955C15.2505 28.8955 15.2075 28.7539 15.1052 28.4171C15.0726 28.3095 15.0339 28.1821 14.9731 28.033L12.9647 22.1189L7.81826 20.5172C6.68855 20.2708 6.68855 19.4083 8.06931 18.9154ZM16.1028 23.5974L25.8937 14.8495C26.2703 14.4799 25.7682 14.2335 25.2661 14.6031L13.7179 21.7493L15.7263 27.6634L16.1028 23.5974Z" fill="white"></path>
                                </svg>
                            </a>
                        </li>
                    </div>
                </div>

                <div class="contacts__form">
                    <h2 class="form-title">Форма заявки</h2>
                    <p class="form-desk">Заполните форму и мы свяжемся с вами</p>

                    <form method="post" action="{{ route('front.leads.store') }}" class="form">
                        @csrf
                        <input type="text"  name="name"  id="name"  autocomplete="name"  placeholder="Имя" required>
                        <input type="text"  name="phone" id="phone" autocomplete="tel"   placeholder="Номер телефона" required>
                        <input type="email" name="email" id="email" autocomplete="email" placeholder="Email">
                        <button class="form-btn" type="submit">Отправить</button>
                    </form>
                </div>
            </section>

            {{-- карта (если в БД что-то есть) --}}
            {{-- карта (если в БД что-то есть) --}}
            @php
                $raw = trim($c['map_embed'] ?? '');
                $src = null;

                if ($raw) {
                    // Если админка получила целый HTML (div/a/iframe) — достанем src
                    if (\Illuminate\Support\Str::contains($raw, '<iframe')) {
                        if (preg_match('/src="([^"]+)"/', $raw, $m)) {
                            $src = $m[1];
                        }
                    } else {
                        // Если в админку вставили просто URL — используем его
                        $src = $raw;
                    }
                }
            @endphp

            @if($src)
                <section class="map">
                    <div class="map-wrap">
                        <iframe
                            src="{{ $src }}"
                            frameborder="0"
                            allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </section>
            @endif
        </div>
    </main>
@endsection
