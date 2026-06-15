@extends('layouts.app')

@section('title', 'Замовлення оформлено - CLOTHSTORE')

@section('content')

<main class="checkout-page">
    <section class="checkout-success-section">
        <div class="container">
            <div class="checkout-success-box">
                <span class="hero-badge">SUCCESS</span>
                <h1>Дякуємо за замовлення!</h1>
                <p>Твоє замовлення <strong>#{{ (int) $order->id }}</strong> успішно прийнято.
                   Ми звʼяжемося з тобою для підтвердження.</p>

                <div class="checkout-success-info">
                    <div class="profile-info-item">
                        <span>Сума</span>
                        <strong>{{ number_format((float) $order->total_amount, 0, '.', ' ') }} грн</strong>
                    </div>
                    <div class="profile-info-item">
                        <span>Статус</span>
                        <strong>{{ $order->status }}</strong>
                    </div>
                </div>

                <div class="checkout-success-actions">
                    <a href="{{ url('/profile/order/' . $order->id) }}" class="btn btn-dark">Переглянути замовлення</a>
                    <a href="{{ url('/catalog') }}" class="btn btn-light">Продовжити покупки</a>
                </div>
            </div>
        </div>
    </section>
</main>

@endsection
