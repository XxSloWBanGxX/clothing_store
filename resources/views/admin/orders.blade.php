@extends('admin.layout')

@section('title', 'Замовлення')

@section('admin_content')
@include('partials.admin-flash')

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Замовлення</h2>
            <p>{{ count($orders) }} записів</p>
        </div>
    </div>

    <form action="{{ url('/admin/orders') }}" method="GET" class="adm-toolbar">
        <div class="adm-search">
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="ID, імʼя, телефон, email…">
        </div>
        <select name="status" class="adm-select">
            <option value="">Усі статуси</option>
            @foreach ($statusLabels as $value => $label)
                <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-dark btn-sm">Фільтрувати</button>
        @if (! empty($filters['q']) || ! empty($filters['status']))
            <a href="{{ url('/admin/orders') }}" class="btn btn-light btn-sm">Скинути</a>
        @endif
    </form>

    @if (count($orders) > 0)
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Клієнт</th>
                        <th>Контакти</th>
                        <th>Сума</th>
                        <th>Статус</th>
                        <th>Дата</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>#{{ (int) $order->id }}</td>
                            <td>
                                <strong>{{ $order->full_name }}</strong>
                                <span class="adm-cell-muted">{{ $order->username ?? 'Гість' }}</span>
                            </td>
                            <td>
                                <span>{{ $order->phone }}</span>
                                <span class="adm-cell-muted">{{ $order->email }}</span>
                            </td>
                            <td>{{ number_format((float) $order->total_amount, 0, '.', ' ') }} грн</td>
                            <td>@include('partials.admin-order-status', ['status' => $order->status])</td>
                            <td>{{ $order->created_at }}</td>
                            <td><a href="{{ url('/admin/orders/' . $order->id) }}" class="btn btn-dark btn-sm">Деталі</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="adm-empty">
            <h3>Замовлень не знайдено</h3>
            <p>Зміни фільтри або зачекай на перше замовлення.</p>
        </div>
    @endif
</section>
@endsection
