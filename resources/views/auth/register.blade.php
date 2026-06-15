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
