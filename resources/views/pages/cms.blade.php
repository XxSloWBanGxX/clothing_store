@extends('layouts.app')

@section('title', $page->title . ' - CLOTHSTORE')

@section('content')
<main class="cms-page">
    <section class="catalog-hero catalog-hero-compact">
        <div class="container">
            <div class="catalog-hero-box">
                <span class="hero-badge">{{ strtoupper($page->slug) }}</span>
                <h1>{{ $page->title }}</h1>
                @if (! empty($page->subtitle))
                    <p>{{ $page->subtitle }}</p>
                @endif
            </div>
        </div>
    </section>

    <section class="cms-content-section">
        <div class="container">
            <div class="cms-content-card">
                @if (! empty($page->content))
                    @foreach (preg_split("/\r\n|\n|\r/", trim($page->content)) as $paragraph)
                        @if (trim($paragraph) !== '')
                            <p>{{ $paragraph }}</p>
                        @endif
                    @endforeach
                @else
                    <p class="cms-empty">Контент скоро зʼявиться.</p>
                @endif
            </div>
        </div>
    </section>
</main>
@endsection
