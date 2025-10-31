{{-- resources/views/pages/posts.blade.php --}}
@extends('layouts.front')

@section('title', $seo['title'] ?? ($pageTitle ?? 'Новости и акции'))
@if(!empty($seo['desc'])) @section('meta_description', $seo['desc']) @endif

@section('content')
    <main class="pages">
        <div class="container">
            <nav class="breadcrumbs" aria-label="Хлебные крошки">
                <ol>
                    <li><a href="{{ url('/') }}">Главная</a></li>
                    <li aria-current="page">{{ $seo['h1'] ?? ($pageTitle ?? 'Новости и акции') }}</li>
                </ol>
            </nav>
        </div>

        <div class="news-page">
            <div class="novelty__wrapper container">
                <h2 class="title">{{ $seo['h1'] ?? ($pageTitle ?? 'Новости и акции') }}</h2>

                <div class="novelty__wrapp">
                    @forelse($items as $post)
                        <a href="#!" class="novelty__card news__card">
                            <div class="novelty__img">
                                <img class="bg-img"
                                     src="{{ $post->getFirstMediaUrl('cover','large') ?: $post->getFirstMediaUrl('cover') }}"
                                     alt="{{ $post->title }}">
                            </div>

                            <h3>{{ $post->title }}</h3>

                            @if($post->excerpt)
                                <p>{{ $post->excerpt }}</p>
                            @endif

                            @php $d = optional($post->published_at); @endphp
                            <div class="novelty__text">
                                <span>{{ $d?->format('d.m.Y') }}</span>
                                <span>
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="8" cy="8" r="8" transform="matrix(-1 0 0 1 17 1)" stroke="#605E5D"/>
                                        <path d="M9 6V10H12.5" stroke="#605E5D"/>
                                    </svg>
                                    {{ $d?->format('H:i') }}
                                </span>
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="20" cy="20" r="20" transform="matrix(-1 0 0 1 40 0)" fill="#6D031A"></circle>
                                    <path d="M14 19.25C13.5858 19.25 13.25 19.5858 13.25 20C13.25 20.4142 13.5858 20.75 14 20.75V20V19.25ZM25.1429 20.75C25.5571 20.75 25.8929 20.4142 25.8929 20C25.8929 19.5858 25.5571 19.25 25.1429 19.25V20V20.75ZM20.5303 13.4697C20.2374 13.1768 19.7626 13.1768 19.4697 13.4697C19.1768 13.7626 19.1768 14.2374 19.4697 14.5303L20 14L20.5303 13.4697ZM26 20L26.5303 20.5303C26.8232 20.2374 26.8232 19.7626 26.5303 19.4697L26 20ZM19.4697 25.4697C19.1768 25.7626 19.1768 26.2374 19.4697 26.5303C19.7626 26.8232 20.2374 26.8232 20.5303 26.5303L20 26L19.4697 25.4697ZM14 20V20.75H25.1429V20V19.25H14V20ZM20 14L19.4697 14.5303L25.4697 20.5303L26 20L26.5303 19.4697L20.5303 13.4697L20 14ZM26 20L25.4697 19.4697L19.4697 25.4697L20 26L20.5303 26.5303L26.5303 20.5303L26 20Z" fill="white"></path>
                                </svg>
                            </div>
                        </a>
                    @empty
                        <p>Пока нет записей.</p>
                    @endforelse
                </div>
            </div>

            @if ($items->hasPages())
                @php
                    $cur=$items->currentPage(); $last=$items->lastPage();
                    $start=max(1,$cur-1); $end=min($last,$cur+1);
                @endphp
                <ul class="paginations">
                    <li>
                        <a href="{{ $items->previousPageUrl() ?: '#!' }}" class="page-btn prev {{ $items->onFirstPage() ? 'disabled' : '' }}">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.06 12L11 11.06L7.94667 8L11 4.94L10.06 4L6.06 8L10.06 12Z" fill="black"/>
                            </svg>
                        </a>
                    </li>

                    @if($start>1)
                        <li><a href="{{ $items->url(1) }}" class="page-btn">1</a></li>
                        @if($start>2)<li><a href="#!" class="page-btn dots">...</a></li>@endif
                    @endif

                    @for($i=$start;$i<=$end;$i++)
                        <li><a href="{{ $items->url($i) }}" class="page-btn {{ $i==$cur?'active':'' }}">{{ $i }}</a></li>
                    @endfor

                    @if($end<$last)
                        @if($end<$last-1)<li><a href="#!" class="page-btn dots">...</a></li>@endif
                        <li><a href="{{ $items->url($last) }}" class="page-btn">{{ $last }}</a></li>
                    @endif

                    <li>
                        <a href="{{ $items->nextPageUrl() ?: '#!' }}" class="page-btn next {{ $cur==$last ? 'disabled' : '' }}">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.94 4L6 4.94L9.05333 8L6 11.06L6.94 12L10.94 8L6.94 4Z" fill="black"/>
                            </svg>
                        </a>
                    </li>
                </ul>
            @endif
        </div>
    </main>
@endsection
