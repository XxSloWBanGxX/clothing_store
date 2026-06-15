@extends('layouts.app')

@section('title', 'Реєстрація - CLOTHSTORE')

@section('content')

<main class="auth-page">
    <div class="container">
        <div class="auth-wrapper">
            <div class="auth-card">
                <span class="hero-badge">REGISTER</span>
                <h1 class="auth-title">Створення акаунта</h1>
                <p class="auth-subtitle">Заповни дані для створення профілю.</p>

                @if ($errors->has('general'))
                    <div class="alert-error">{{ $errors->first('general') }}</div>
                @endif

                <div class="social-auth">
                    <a href="{{ url('/auth/google/redirect') }}" class="social-btn social-google">
                        <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.6 20.5h-1.9V20H24v8h11.3c-1.6 4.7-6.1 8-11.3 8-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.6 6.1 29.6 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.3-.4-3.5z"/><path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 16 19 13 24 13c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.6 6.1 29.6 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/><path fill="#4CAF50" d="M24 44c5.5 0 10.5-2.1 14.3-5.6l-6.6-5.6C29.6 34.6 26.9 36 24 36c-5.2 0-9.6-3.3-11.3-8l-6.5 5C9.6 39.6 16.2 44 24 44z"/><path fill="#1976D2" d="M43.6 20.5H24v8h11.3c-.8 2.3-2.3 4.2-4.2 5.6l6.6 5.6c-.5.4 7.3-5.3 7.3-15.7 0-1.3-.1-2.3-.4-3.5z"/></svg>
                        Реєстрація через Google
                    </a>
                </div>

                <div class="auth-divider"><span>або</span></div>

                <form action="{{ url('/register') }}" method="POST" class="auth-form">
                    @csrf

                    <div class="form-group">
                        <label for="name">Імʼя</label>
                        <input type="text" id="name" name="name" placeholder="Введи імʼя"
                               value="{{ old('name') }}">
                        @error('name')
                            <small class="form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Наприклад: bogdan_01"
                               value="{{ old('username') }}">
                        @error('username')
                            <small class="form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Введи email"
                               value="{{ old('email') }}">
                        @error('email')
                            <small class="form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Номер телефону</label>
                        <input type="text" id="phone" name="phone" placeholder="+380..."
                               value="{{ old('phone') }}">
                        @error('phone')
                            <small class="form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" id="password" name="password" placeholder="Мінімум 6 символів">
                        @error('password')
                            <small class="form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Підтвердження пароля</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Повтори пароль">
                        @error('confirm_password')
                            <small class="form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-dark auth-btn">Зареєструватися</button>
                </form>

                <p class="auth-switch">
                    Уже маєш акаунт?
                    <a href="{{ url('/login') }}">Увійти</a>
                </p>
            </div>
        </div>
    </div>
</main>

@endsection
