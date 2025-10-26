{{-- resources/views/pages/privacy.blade.php --}}
@extends('layouts.front')

@php
    $title = 'Политика конфиденциальности';
@endphp

@section('title', $title)

@section('content')
    <main class="pages">
        <div class="container">
            <nav class="breadcrumbs" aria-label="Хлебные крошки">
                <ol>
                    <li><a href="{{ route('front.home') }}">Главная</a></li>
                    <li aria-current="page">{{ $title }}</li>
                </ol>
            </nav>
        </div>

        <div class="centeres">
            <section class="privacy container">
                <span class="decor-text">Создаем комфорт</span>

                <h1 class="title">{{ $title }}</h1>

                <div class="privacy__content">
                    <p>Здесь разместите текст вашей политики конфиденциальности: какие данные собираете, цели обработки,
                        хранение, права пользователя, контакты для запросов и т. д.</p>

                    <h2>1. Общие положения</h2>
                    <p>…</p>

                    <h2>2. Сбор и обработка данных</h2>
                    <p>…</p>

                    <h2>3. Хранение и защита</h2>
                    <p>…</p>

                    <h2>4. Контакты</h2>
                    @php $c = site_contacts(); @endphp
                    <ul>
                        @if(!empty($c->email)) <li>Email: <a href="mailto:{{ $c->email }}">{{ $c->email }}</a></li> @endif
                        @foreach(($c->phones ?? []) as $p)
                            @if(!empty($p['number'])) <li>Тел.: <a href="{{ tel_href($p['number']) }}">{{ $p['number'] }}</a></li> @endif
                        @endforeach
                    </ul>
                </div>
            </section>
        </div>
    </main>
@endsection
