@extends('layouts.front')

@php
    $meta = $meta ?? [];
    $title = $meta['title'] ?? ($title ?? 'Политика конфиденциальности');
    $desc = $meta['description'] ?? null;
@endphp

@section('title', $title)
@if($desc) @section('meta_description', $desc) @endif

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

        {{-- СОВПАДАЕТ С ВЕРСТКОЙ --}}
        <section class="privacy">
            <div class="container">
                <h2 class="privacy__title">{{ $title }}</h2>

                <div class="privacy__texts privacy__content">
                    {!! $body_html !!}
                </div>
            </div>
        </section>
    </main>
@endsection
