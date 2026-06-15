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
                    <div class="profile-info-list">
                        <div class="profile-info-item">
                            <span>Ім’я</span>
                            <strong>{{ $user->name ?? '—' }}</strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Username</span>
                            <strong>{{ $user->username ?? '—' }}</strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Email</span>
                            <strong>{{ $user->email ?? '—' }}</strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Телефон</span>
                            <strong>{{ $user->phone ?? '—' }}</strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Роль</span>
                            <strong>{{ $user->role ?? 'user' }}</strong>
                        </div>
                    </div>
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
