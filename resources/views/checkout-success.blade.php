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
                    @if (($order->payment_status ?? '') === 'paid')
                        Оплату карткою підтверджено.
                    @else
                        Ми звʼяжемося з тобою для підтвердження.
                    @endif
                </p>

                <div class="checkout-success-info">
                    <div class="profile-info-item">
                        <span>Сума</span>
                        <strong>{{ number_format((float) $order->total_amount, 0, '.', ' ') }} грн</strong>
                    </div>
                    <div class="profile-info-item">
                        <span>Статус</span>
                        <strong>{{ $order->status }}</strong>
                    </div>
                    @if (! empty($order->payment_status))
                        <div class="profile-info-item">
                            <span>Оплата</span>
                            <strong>{{ $order->payment_status === 'paid' ? 'Оплачено' : 'Очікує оплати' }}</strong>
                        </div>
                    @endif
                    @if (! empty($order->card_last4))
                        <div class="profile-info-item">
                            <span>Картка</span>
                            <strong>•••• {{ $order->card_last4 }}</strong>
                        </div>
                    @endif
                    @if (! empty($order->payment_reference))
                        <div class="profile-info-item">
                            <span>Код платежу</span>
                            <strong>{{ $order->payment_reference }}</strong>
                        </div>
                    @endif
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
