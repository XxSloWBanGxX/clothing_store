@extends('layouts.app')

@section('title', 'Замовлення оформлено - CLOTHSTORE')

@section('content')

@php
    $statusLabels = [
        'new' => 'Нове',
        'processing' => 'В обробці',
        'shipped' => 'Відправлено',
        'completed' => 'Виконано',
        'cancelled' => 'Скасовано',
    ];
    $statusLabel = $statusLabels[$order->status ?? ''] ?? ($order->status ?? '—');
@endphp

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
                    <div class="checkout-success-row">
                        <span class="checkout-success-label">Сума</span>
                        <strong class="checkout-success-value">{{ number_format((float) $order->total_amount, 0, '.', ' ') }} грн</strong>
                    </div>
                    <div class="checkout-success-row">
                        <span class="checkout-success-label">Статус</span>
                        <strong class="checkout-success-value">{{ $statusLabel }}</strong>
                    </div>
                    @if (! empty($order->payment_status))
                        <div class="checkout-success-row">
                            <span class="checkout-success-label">Оплата</span>
                            <strong class="checkout-success-value">{{ $order->payment_status === 'paid' ? 'Оплачено' : 'Очікує оплати' }}</strong>
                        </div>
                    @endif
                    @if (! empty($order->card_last4))
                        <div class="checkout-success-row">
                            <span class="checkout-success-label">Картка</span>
                            <strong class="checkout-success-value">•••• {{ $order->card_last4 }}</strong>
                        </div>
                    @endif
                    @if (! empty($order->payment_reference))
                        <div class="checkout-success-row">
                            <span class="checkout-success-label">Код платежу</span>
                            <strong class="checkout-success-value checkout-success-value--code">{{ $order->payment_reference }}</strong>
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
