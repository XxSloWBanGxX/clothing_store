@extends('layouts.app')

@section('title', 'Профіль - CLOTHSTORE')

@section('content')

<main class="profile-page">
    <section class="catalog-hero">
        <div class="container">
            <div class="catalog-hero-box">
                <span class="hero-badge">PROFILE</span>
                <h1>Мій профіль</h1>
                <p>Переглядай свої дані, змінюй пароль і відстежуй замовлення.</p>
            </div>
        </div>
    </section>

    <section class="profile-section">
        <div class="container">
            <div class="profile-grid">
                <div class="profile-card">
                    <h2>Особисті дані</h2>

                    @if (session('profileSuccess'))
                        <div class="profile-success-message">{{ session('profileSuccess') }}</div>
                    @endif

                    <form action="{{ url('/profile/update') }}" method="POST" class="profile-password-form">
                        @csrf

                        <div class="form-group">
                            <label for="name">Імʼя</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
                            @error('name')<small class="form-error">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}">
                            @error('username')<small class="form-error">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
                            @error('email')<small class="form-error">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label for="phone">Телефон</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')<small class="form-error">{{ $message }}</small>@enderror
                        </div>

                        <button type="submit" class="btn btn-dark profile-password-btn">Зберегти дані</button>
                    </form>
                </div>

                <div class="profile-card">
                    <h2>Змінити пароль</h2>

                    @if (session('passwordSuccess'))
                        <div class="profile-success-message">{{ session('passwordSuccess') }}</div>
                    @endif

                    <form action="{{ url('/profile/password') }}" method="POST" class="profile-password-form">
                        @csrf

                        <div class="form-group">
                            <label for="current_password">Поточний пароль</label>
                            <input type="password" id="current_password" name="current_password">
                            @error('current_password')<small class="form-error">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label for="new_password">Новий пароль</label>
                            <input type="password" id="new_password" name="new_password">
                            @error('new_password')<small class="form-error">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Підтверди новий пароль</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                            @error('confirm_password')<small class="form-error">{{ $message }}</small>@enderror
                        </div>

                        <button type="submit" class="btn btn-dark profile-password-btn">Оновити пароль</button>
                    </form>
                </div>
            </div>

            <div class="profile-orders-box">
                <div class="section-head">
                    <div>
                        <span class="section-label">MY ORDERS</span>
                        <h2>Мої замовлення</h2>
                    </div>
                </div>

                @if (count($orders) > 0)
                    <div class="profile-orders-list">
                        @foreach ($orders as $order)
                            <div class="profile-order-item">
                                <div class="profile-order-main">
                                    <strong>Замовлення #{{ (int) $order->id }}</strong>
                                    <span class="profile-order-status">{{ $order->status }}</span>
                                </div>

                                <div class="profile-order-meta">
                                    <span>{{ $order->created_at }}</span>
                                    <strong>{{ number_format((float) $order->total_amount, 0, '.', ' ') }} грн</strong>
                                </div>

                                <a href="{{ url('/profile/order/' . $order->id) }}" class="btn btn-light">Переглянути</a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-box">
                        <h3>У тебе ще немає замовлень</h3>
                        <p>Коли ти оформиш замовлення, воно з’явиться тут.</p>
                        <a href="{{ url('/catalog') }}" class="btn btn-dark">Перейти в каталог</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</main>

@endsection
