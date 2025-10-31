@extends('layouts.front')

@section('title', $title ?? 'Категории')
@if(!empty($desc))
    @section('meta_description', $desc)
@endif


@section('content')
    <main class="pages">

    <div class="container">
        <nav class="breadcrumbs" aria-label="Хлебные крошки">
            <ol>
                <li><a href="{{ url('/') }}">Главная</a></li>
                <li aria-current="page">Категории</li>
            </ol>
        </nav>
    </div>
    <div class="centeres">
        <section class="categories categories-page">
            <span class="decor-text">Создаем комфорт</span>

            <div class="categories__wrapper container">
                <h2 class="title">{{ $h1 ?? 'Категории товаров' }}</h2>

                @if($categories->count())
                    <div class="categories__wrapp">
                        @foreach($categories as $cat)
                            @php
                                $img  = $cat->getFirstMediaUrl('image', 'thumb') ?: $cat->getFirstMediaUrl('image');
                                $href = route('category.show', $cat->slug);
                            @endphp

                            <a href="{{ $href }}" class="categories__card" title="{{ $cat->name }}">
                                <h3>{{ $cat->name }}</h3>
                                @if($img)
                                    <img src="{{ $img }}" alt="{{ $cat->name }}">
                                @endif
                            </a>
                        @endforeach
                    </div>

                    @if($categories->hasPages())
                        <ul class="paginations">
                            {{-- prev --}}
                            <li>
                                <a href="{{ $categories->previousPageUrl() ?: '#!' }}"
                                   class="page-btn prev {{ $categories->onFirstPage() ? 'disabled' : '' }}">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.06 12L11 11.06L7.94667 8L11 4.94L10.06 4L6.06 8L10.06 12Z" fill="black" />
                                    </svg>
                                </a>
                            </li>

                            {{-- numeric --}}
                            @foreach($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                                <li>
                                    <a href="{{ $url }}"
                                       class="page-btn {{ $page == $categories->currentPage() ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>
                                </li>
                            @endforeach

                            {{-- next --}}
                            <li>
                                <a href="{{ $categories->nextPageUrl() ?: '#!' }}"
                                   class="page-btn next {{ $categories->currentPage() == $categories->lastPage() ? 'disabled' : '' }}">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.94 4L6 4.94L9.05333 8L6 11.06L6.94 12L10.94 8L6.94 4Z" fill="black" />
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    @endif
                @else
                    {{-- Пустое состояние. Если хочешь 404 — см. ниже. --}}
                    <div class="container" style="padding:40px 0;">
                        <p style="font-size:18px">Категории скоро появятся 💫</p>
                    </div>
                @endif
            </div>
        </section>
    </div>
    </main>
@endsection
