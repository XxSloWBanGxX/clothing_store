@extends('layouts.app')

@section('title', 'Сторінку не знайдено - CLOTHSTORE')

@section('content')

<main class="error-page">
    <div class="container">
        <div class="error-box">
            <span class="hero-badge">404</span>
            <h1>Сторінку не знайдено</h1>
            <p>Можливо, посилання застаріле або сторінку було видалено.</p>
            <div class="error-actions">
                <a href="{{ url('/') }}" class="btn btn-dark">На головну</a>
                <a href="{{ url('/catalog') }}" class="btn btn-light">У каталог</a>
            </div>
        </div>
    </div>
</main>

@endsection
