@extends('admin.layout')

@section('title', 'Замовлення #' . (int) $order->id)

@section('admin_content')
@include('partials.admin-flash')

@php
    $deliveryLabel = $deliveryLabels[$order->delivery_method] ?? $order->delivery_method;
    $paymentLabel = $paymentLabels[$order->payment_method] ?? $order->payment_method;
@endphp

<section class="adm-order-head">
    <div>
        <a href="{{ url('/admin/orders') }}" class="adm-back-link">← Усі замовлення</a>
        <div class="adm-order-head-meta">
            <h2>Замовлення #{{ (int) $order->id }}</h2>
            @include('partials.admin-order-status', ['status' => $order->status])
        </div>
        <p class="adm-cell-muted">Створено: {{ $order->created_at }}</p>
    </div>
    <div class="adm-order-head-total">
        <span>Сума замовлення</span>
        <strong>{{ number_format((float) $order->total_amount, 0, '.', ' ') }} грн</strong>
    </div>
</section>

<div class="adm-grid-2">
    <section class="adm-panel">
        <div class="adm-panel-head">
            <h2>Клієнт і доставка</h2>
        </div>
        <dl class="adm-dl">
            <div><dt>Користувач</dt><dd>{{ $order->username ?? '—' }}</dd></div>
            <div><dt>Імʼя</dt><dd>{{ $order->full_name }}</dd></div>
            <div><dt>Телефон</dt><dd>{{ $order->phone }}</dd></div>
            <div><dt>Email</dt><dd>{{ $order->email }}</dd></div>
            <div><dt>Місто</dt><dd>{{ $order->city ?: '—' }}</dd></div>
            <div><dt>Адреса</dt><dd>{{ $order->address_line ?: '—' }}</dd></div>
            <div><dt>Доставка</dt><dd>{{ $deliveryLabel }}</dd></div>
            <div><dt>Оплата</dt><dd>{{ $paymentLabel }}</dd></div>
            <div><dt>Коментар</dt><dd>{{ $order->comment ?: '—' }}</dd></div>
        </dl>
    </section>

    <section class="adm-panel">
        <div class="adm-panel-head">
            <h2>Статус замовлення</h2>
        </div>

        <form action="{{ url('/admin/orders/' . $order->id . '/status') }}" method="POST" class="adm-form">
            @csrf
            <div class="form-group">
                <label for="status">Статус</label>
                <select id="status" name="status" class="adm-select adm-select--block">
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-dark">Зберегти статус</button>
        </form>

        <div class="adm-status-steps">
            @foreach ($statusLabels as $value => $label)
                @if ($value !== 'cancelled')
                    <span class="adm-status-step {{ $order->status === $value ? 'is-current' : '' }}">{{ $label }}</span>
                @endif
            @endforeach
        </div>
    </section>
</div>

<section class="adm-panel">
    <div class="adm-panel-head">
        <h2>Товари в замовленні</h2>
    </div>

    <div class="adm-table-wrap">
        <table class="adm-table">
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
</section>
@endsection
