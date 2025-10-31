{{-- resources/views/errors/404.blade.php --}}
@extends('layouts.front')

@section('title', '404 — Страница не найдена')
@section('meta_description', 'Страница не найдена')

@section('content')
    <main class="pages">
        <div class="container">
            <nav class="breadcrumbs" aria-label="Хлебные крошки">
                <ol>
                    <li><a href="{{ url('/') }}">Главная</a></li>
                    <li aria-current="page">404</li>
                </ol>
            </nav>
        </div>

        <section class="not-found">
            <div>
                <img src="{{ asset('img/not.png') }}" alt="">
            </div>
        </section>
    </main>
@endsection
