@extends('admin.layout')

@section('title', 'Перегляд замовлення')

@section('admin_content')

@if (session('status'))
    <div class="alert-success">{{ session('status') }}</div>
@endif

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Замовлення #{{ (int) $order->id }}</h2>
    </div>

    <div class="admin-order-grid">
        <div class="admin-order-card">
            <h3>Інформація про клієнта</h3>
            <p><strong>Користувач:</strong> {{ $order->username ?? '—' }}</p>
            <p><strong>Ім’я:</strong> {{ $order->full_name }}</p>
            <p><strong>Телефон:</strong> {{ $order->phone }}</p>
            <p><strong>Email:</strong> {{ $order->email }}</p>
            <p><strong>Місто:</strong> {{ $order->city }}</p>
            <p><strong>Адреса / відділення:</strong> {{ $order->address_line }}</p>
            <p><strong>Доставка:</strong> {{ $order->delivery_method }}</p>
            <p><strong>Оплата:</strong> {{ $order->payment_method }}</p>
            <p><strong>Коментар:</strong> {{ $order->comment ?: '—' }}</p>
        </div>

        <div class="admin-order-card">
            <h3>Статус замовлення</h3>

            <form action="{{ url('/admin/orders/' . $order->id . '/status') }}" method="POST" class="admin-order-status-form">
                @csrf

                <div class="form-group">
                    <label for="status">Статус</label>
                    <select id="status" name="status">
                        @foreach (['new', 'processing', 'sent', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-dark">Оновити статус</button>
            </form>

            <div class="admin-order-total">
                <strong>Разом:</strong> {{ number_format((float) $order->total_amount, 0, '.', ' ') }} грн
            </div>
        </div>
    </div>

    <div class="admin-order-card admin-order-items-card">
        <h3>Товари в замовленні</h3>

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
    </div>

    <div class="admin-form-actions">
        <a href="{{ url('/admin/orders') }}" class="btn btn-light">Назад до замовлень</a>
    </div>
</section>

@endsection
