@extends('admin.layout')

@section('title', 'Замовлення')

@section('admin_content')

@if (session('status'))
    <div class="alert-success">{{ session('status') }}</div>
@endif

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Замовлення</h2>
    </div>

    @if (count($orders) > 0)
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Користувач</th>
                        <th>Ім’я</th>
                        <th>Телефон</th>
                        <th>Сума</th>
                        <th>Статус</th>
                        <th>Дата</th>
                        <th>Дія</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>#{{ (int) $order->id }}</td>
                            <td>{{ $order->username ?? '—' }}</td>
                            <td>{{ $order->full_name }}</td>
                            <td>{{ $order->phone }}</td>
                            <td>{{ number_format((float) $order->total_amount, 0, '.', ' ') }} грн</td>
                            <td><span class="admin-badge">{{ $order->status }}</span></td>
                            <td>{{ $order->created_at }}</td>
                            <td>
                                <a href="{{ url('/admin/orders/' . $order->id) }}" class="btn btn-light btn-sm">Переглянути</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-box">
            <h3>Замовлень ще немає</h3>
            <p>Коли користувачі почнуть оформляти замовлення, вони з’являться тут.</p>
        </div>
    @endif
</section>

@endsection
