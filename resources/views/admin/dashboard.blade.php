@extends('admin.layout')

@section('title', 'Дашборд')

@section('admin_content')
@include('partials.admin-flash')

@php
    $statusLabels = $statusLabels ?? \App\Http\Controllers\AdminController::orderStatusLabels();
@endphp

<section class="adm-kpi-grid">
    <article class="adm-kpi-card adm-kpi-card--accent">
        <span class="adm-kpi-label">Виручка за місяць</span>
        <strong class="adm-kpi-value">{{ number_format((float) $stats['revenueMonth'], 0, '.', ' ') }} грн</strong>
        <small>Всього: {{ number_format((float) $stats['revenueTotal'], 0, '.', ' ') }} грн</small>
    </article>
    <article class="adm-kpi-card">
        <span class="adm-kpi-label">Замовлення</span>
        <strong class="adm-kpi-value">{{ (int) $stats['orders'] }}</strong>
        <small>{{ (int) $stats['ordersNew'] }} нових</small>
    </article>
    <article class="adm-kpi-card">
        <span class="adm-kpi-label">Товари</span>
        <strong class="adm-kpi-value">{{ (int) $stats['products'] }}</strong>
        <small>{{ (int) $stats['inStock'] }} в наявності</small>
    </article>
    <article class="adm-kpi-card">
        <span class="adm-kpi-label">Клієнти</span>
        <strong class="adm-kpi-value">{{ (int) $stats['users'] }}</strong>
        <small>{{ (int) $stats['reviews'] }} відгуків</small>
    </article>
</section>

<section class="adm-grid-2">
    <div class="adm-panel">
        <div class="adm-panel-head">
            <div>
                <h2>Останні замовлення</h2>
                <p>Найсвіжіші замовлення магазину</p>
            </div>
            <a href="{{ url('/admin/orders') }}" class="btn btn-light btn-sm">Усі замовлення</a>
        </div>

        @if ($recentOrders->isNotEmpty())
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Клієнт</th>
                            <th>Сума</th>
                            <th>Статус</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                            <tr>
                                <td>#{{ (int) $order->id }}</td>
                                <td>
                                    <strong>{{ $order->full_name }}</strong>
                                    <span class="adm-cell-muted">{{ $order->phone }}</span>
                                </td>
                                <td>{{ number_format((float) $order->total_amount, 0, '.', ' ') }} грн</td>
                                <td>@include('partials.admin-order-status', ['status' => $order->status])</td>
                                <td><a href="{{ url('/admin/orders/' . $order->id) }}" class="adm-link">Відкрити</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="adm-empty">
                <p>Замовлень ще немає.</p>
            </div>
        @endif
    </div>

    <div class="adm-panel">
        <div class="adm-panel-head">
            <div>
                <h2>Низький залишок</h2>
                <p>Товари зі stock ≤ 5</p>
            </div>
            <a href="{{ url('/admin/products?stock=low') }}" class="btn btn-light btn-sm">Переглянути</a>
        </div>

        @if ($lowStockProducts->isNotEmpty())
            <ul class="adm-stock-list">
                @foreach ($lowStockProducts as $product)
                    <li class="adm-stock-item">
                        <div class="adm-stock-thumb">
                            @if (! empty($product->image))
                                <img src="{{ asset('assets/images/products/' . $product->image) }}" alt="">
                            @else
                                <span>{{ mb_substr($product->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div class="adm-stock-copy">
                            <strong>{{ $product->name }}</strong>
                            <span>{{ $product->category_name }}</span>
                        </div>
                        <span class="adm-badge {{ (int) $product->stock <= 0 ? 'adm-badge--danger' : 'adm-badge--warning' }}">
                            {{ (int) $product->stock }} шт
                        </span>
                        <a href="{{ url('/admin/products/' . $product->id . '/edit') }}" class="adm-link">Редагувати</a>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="adm-empty">
                <p>Усі товари мають достатній залишок.</p>
            </div>
        @endif
    </div>
</section>

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Швидкі дії</h2>
            <p>Найчастіші операції адміністратора</p>
        </div>
    </div>
    <div class="adm-quick-grid">
        <a href="{{ url('/admin/products/create') }}" class="adm-quick-card">
            <span class="adm-quick-icon">＋</span>
            <strong>Додати товар</strong>
            <small>Новий SKU в каталог</small>
        </a>
        <a href="{{ url('/admin/orders?status=new') }}" class="adm-quick-card">
            <span class="adm-quick-icon">🛒</span>
            <strong>Нові замовлення</strong>
            <small>{{ (int) $stats['ordersNew'] }} очікують</small>
        </a>
        <a href="{{ url('/admin/categories') }}" class="adm-quick-card">
            <span class="adm-quick-icon">☰</span>
            <strong>Категорії</strong>
            <small>{{ (int) $stats['categories'] }} активних</small>
        </a>
        <a href="{{ url('/admin/support?status=new') }}" class="adm-quick-card">
            <span class="adm-quick-icon">💬</span>
            <strong>Підтримка</strong>
            <small>{{ (int) $stats['support'] }} нових</small>
        </a>
    </div>
</section>
@endsection
