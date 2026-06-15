@extends('layouts.app')

@section('title', 'Моє замовлення - CLOTHSTORE')

@section('content')

<main class="profile-page">
    <section class="catalog-hero">
        <div class="container">
            <div class="catalog-hero-box">
                <span class="hero-badge">ORDER</span>
                <h1>Замовлення #{{ (int) $order->id }}</h1>
                <p>Деталі твого замовлення та його статус.</p>
            </div>
        </div>
    </section>

    <section class="profile-section">
        <div class="container">
            @if (session('orderSuccess'))
                <div class="alert-success">{{ session('orderSuccess') }}</div>
            @endif
            @if (session('orderError'))
                <div class="alert-error">{{ session('orderError') }}</div>
            @endif

            <div class="profile-grid">
                <div class="profile-card">
                    <h2>Інформація про доставку</h2>
                    <div class="profile-info-list">
                        <div class="profile-info-item">
                            <span>Ім’я</span>
                            <strong>{{ $order->full_name }}</strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Телефон</span>
                            <strong>{{ $order->phone }}</strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Email</span>
                            <strong>{{ $order->email }}</strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Місто</span>
                            <strong>{{ $order->city }}</strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Адреса</span>
                            <strong>{{ $order->address_line }}</strong>
                        </div>
                    </div>
                </div>

                <div class="profile-card">
                    <h2>Деталі замовлення</h2>
                    <div class="profile-info-list">
                        <div class="profile-info-item">
                            <span>Статус</span>
                            <strong>{{ $order->status }}</strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Доставка</span>
                            <strong>{{ $order->delivery_method }}</strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Оплата</span>
                            <strong>{{ $order->payment_method }}</strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Коментар</span>
                            <strong>{{ $order->comment ?: '—' }}</strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Разом</span>
                            <strong>{{ number_format((float) $order->total_amount, 0, '.', ' ') }} грн</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-orders-box">
                <div class="section-head">
                    <div>
                        <span class="section-label">ITEMS</span>
                        <h2>Товари в замовленні</h2>
                    </div>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Товар</th>
                                <th>Ціна</th>
                                <th>К-сть</th>
                                <th>Розмір</th>
                                <th>Колір</th>
                                <th>Сума</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ number_format((float) $item->product_price, 0, '.', ' ') }} грн</td>
                                    <td>{{ (int) $item->quantity }}</td>
                                    <td>{{ $item->selected_size ?: '—' }}</td>
                                    <td>{{ $item->selected_color_name ?: '—' }}</td>
                                    <td>{{ number_format((float) $item->product_price * (int) $item->quantity, 0, '.', ' ') }} грн</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="{{ url('/profile') }}" class="btn btn-light">Назад до профілю</a>

                    @if (in_array($order->status, ['new', 'processing'], true))
                        <form action="{{ url('/profile/order/' . $order->id . '/cancel') }}" method="POST" onsubmit="return confirm('Скасувати це замовлення?');">
                            @csrf
                            <button type="submit" class="btn btn-dark">Скасувати замовлення</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>
</main>

@endsection
