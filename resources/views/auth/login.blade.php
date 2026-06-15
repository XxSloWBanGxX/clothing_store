@extends('layouts.app')

@section('title', 'Вхід - CLOTHSTORE')

@section('content')

<main class="auth-page">
    <div class="container">
        <div class="auth-wrapper">
            <div class="auth-card">
                <span class="hero-badge">LOGIN</span>
                <h1 class="auth-title">Вхід в акаунт</h1>
                <p class="auth-subtitle">Увійди через email або username.</p>

                @if ($errors->has('general'))
                    <div class="alert-error">{{ $errors->first('general') }}</div>
                @endif

                <form action="{{ url('/login') }}" method="POST" class="auth-form">
                    @csrf

                    <div class="form-group">
                        <label for="login">Email або username</label>
                        <input
                            type="text"
                            id="login"
                            name="login"
                            placeholder="Введи email або username"
                            value="{{ old('login') }}"
                        >
                        @error('login')
                            <small class="form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Введи пароль"
                        >
                        @error('password')
                            <small class="form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-dark auth-btn">Увійти</button>
                </form>

                <p class="auth-switch">
                    Немає акаунта?
                    <a href="{{ url('/register') }}">Зареєструватися</a>
                </p>
            </div>
        </div>
    </div>
</main>

@endsection
