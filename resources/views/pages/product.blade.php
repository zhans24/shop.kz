{{-- resources/views/products/show.blade.php --}}
@extends('layouts.front')

@section('title', $product->name)

@section('content')
    <section class="container">
        <h1>{{ $product->name }}</h1>
        @if($product->coverUrl())
            <img src="{{ $product->coverUrl('large') }}" alt="{{ $product->name }}">
        @endif

        @php
            $hasDiscount = $product->hasActiveDiscount();
        @endphp

        @if($hasDiscount)
            <div class="price-wrap">
                <span class="price price--new">{{ number_format($product->finalPrice(), 2, '.', ' ') }} ₸</span>
                <span class="price price--old">{{ number_format($product->price, 2, '.', ' ') }} ₸</span>
            </div>
        @else
            <p class="price">{{ number_format($product->price, 2, '.', ' ') }} ₸</p>
        @endif

        <div class="mt-4">{!! nl2br(e($product->description)) !!}</div>
    </section>
@endsection
