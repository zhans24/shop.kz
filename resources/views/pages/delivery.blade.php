@extends('layouts.front')

@section('title', $meta['title'] ?? $title ?? 'TechnoStyle')
@if(!empty($meta['description']))
    @section('meta_description', $meta['description'])
@endif

@section('content')
    <main class="pages">
        <div class="container">
            <nav class="breadcrumbs" aria-label="Хлебные крошки">
                <ol>
                    <li><a href="{{ route('front.home') }}">Главная</a></li>
                    <li aria-current="page">{{ $title ?? 'Доставка и оплата' }}</li>
                </ol>
            </nav>
        </div>

        <div class="centeres">
            <section class="products-page payment-page">
                @if(!empty($decor_text))
                    <span class="decor-text">{{ $decor_text }}</span>
                @endif

                <section class="payment">
                    <div class="container">
                        <div class="payment__inner">
                            <div class="payment__img">
                                @if(!empty($image))
                                    <img src="{{ $image }}" alt="">
                                @endif
                            </div>
                            <div class="payment__info">
                                <h2 class="payment__title">{{ data_get($payment,'title',$title) }}</h2>

                                @if(!empty($payment['points']))
                                    <ul>
                                        @foreach($payment['points'] as $p)
                                            <li>
                                                <div>
                                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="12.5" cy="12.5" r="12.5" fill="#3EC337" />
                                                        <path d="M18.0545 8.33337L10.4156 15.9723L6.94336 12.5"
                                                              stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                                <p>{{ $p['text'] ?? '' }}</p>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            </section>
        </div>
    </main>
@endsection
