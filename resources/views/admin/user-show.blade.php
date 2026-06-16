@extends('admin.layout')

@section('title', 'Користувач · ' . $user->name)

@section('admin_content')
@include('partials.admin-flash')

@php
    $roleTone = match ($user->role) {
        'admin' => 'dark',
        'support' => 'info',
        default => 'neutral',
    };
@endphp

<section class="adm-user-head">
    <div class="adm-user-head-main">
        <a href="{{ url('/admin/users') }}" class="adm-back-link">← Усі користувачі</a>
        <div class="adm-user-identity">
            <div class="adm-user-avatar">
                @if (! empty($user->avatar))
                    <img src="{{ asset('assets/images/avatars/' . $user->avatar) }}" alt="">
                @else
                    <span>{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                @endif
            </div>
            <div>
                <div class="adm-user-title-row">
                    <h2>{{ $user->name }}</h2>
                    <span class="adm-badge adm-badge--{{ $roleTone }}">{{ $user->role }}</span>
                    @if (! empty($user->is_verified))
                        <span class="adm-badge adm-badge--success">Підтверджений</span>
                    @else
                        <span class="adm-badge adm-badge--warning">Не підтверджений</span>
                    @endif
                </div>
                <p class="adm-cell-muted">{{ '@' . $user->username }} · #{{ (int) $user->id }}</p>
            </div>
        </div>
    </div>
    <div class="adm-panel-actions">
        <a href="{{ url('/admin/messages') }}" class="btn btn-light btn-sm">✉ Повідомлення</a>
        @if ((int) $user->id !== (int) auth()->id())
            <form action="{{ url('/admin/users/' . $user->id) }}" method="POST" onsubmit="return confirm('Видалити користувача?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-light btn-sm">Видалити</button>
            </form>
        @endif
    </div>
</section>

<section class="adm-kpi-grid adm-kpi-grid--user">
    <article class="adm-kpi-card">
        <span class="adm-kpi-label">Замовлень</span>
        <strong class="adm-kpi-value">{{ (int) $stats['orders'] }}</strong>
        <small>{{ $stats['last_order_at'] ? 'Останнє: ' . $stats['last_order_at'] : 'Ще не замовляв' }}</small>
    </article>
    <article class="adm-kpi-card adm-kpi-card--accent">
        <span class="adm-kpi-label">Сума покупок</span>
        <strong class="adm-kpi-value">{{ number_format((float) $stats['spent'], 0, '.', ' ') }} грн</strong>
        <small>Середній чек: {{ number_format((float) $stats['avg'], 0, '.', ' ') }} грн</small>
    </article>
    <article class="adm-kpi-card">
        <span class="adm-kpi-label">Бонуси</span>
        <strong class="adm-kpi-value">{{ (int) $stats['bonus'] }}</strong>
        <small>балів на рахунку</small>
    </article>
    <article class="adm-kpi-card">
        <span class="adm-kpi-label">Відгуки</span>
        <strong class="adm-kpi-value">{{ (int) $stats['reviews'] }}</strong>
        <small>{{ $promocodes->count() }} промокодів</small>
    </article>
</section>

<div class="adm-grid-2">
    <section class="adm-panel">
        <div class="adm-panel-head"><h2>Контакти та акаунт</h2></div>
        <dl class="adm-dl">
            <div><dt>Email</dt><dd>{{ $user->email }}</dd></div>
            <div><dt>Телефон</dt><dd>{{ $user->phone ?: '—' }}</dd></div>
            <div><dt>Username</dt><dd>{{ $user->username }}</dd></div>
            <div><dt>Роль</dt><dd>{{ $user->role }}</dd></div>
            @if (! empty($user->created_at))
                <div><dt>Реєстрація</dt><dd>{{ $user->created_at }}</dd></div>
            @endif
        </dl>
    </section>

    <section class="adm-panel">
        <div class="adm-panel-head"><h2>Збережена доставка</h2></div>
        @php $hasDelivery = false; @endphp
        <div class="adm-delivery-list">
            @foreach ($deliveryAll as $carrier => $entry)
                @if (! empty($entry['city']) || ! empty($entry['branch']))
                    @php $hasDelivery = true; @endphp
                    <div class="adm-delivery-item">
                        <strong>{{ $deliveryLabels[$carrier] ?? $carrier }}</strong>
                        <span>{{ $entry['city'] ?: '—' }}</span>
                        @if (! empty($entry['branch']))
                            <span class="adm-cell-muted">{{ $entry['branch'] }}</span>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
        @if (! $hasDelivery)
            <div class="adm-empty compact"><p>Адреси доставки не збережені.</p></div>
        @endif
    </section>
</div>

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Історія покупок</h2>
            <p>{{ $orders->count() }} замовлень</p>
        </div>
    </div>

    @if ($orders->isNotEmpty())
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Дата</th>
                        <th>Товари</th>
                        <th>Сума</th>
                        <th>Доставка</th>
                        <th>Оплата</th>
                        <th>Статус</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        @php
                            $items = $orderItems[$order->id] ?? collect();
                            $itemsPreview = $items->take(2)->pluck('product_name')->implode(', ');
                        @endphp
                        <tr>
                            <td>#{{ (int) $order->id }}</td>
                            <td>{{ $order->created_at }}</td>
                            <td>
                                <strong>{{ $items->count() }} поз.</strong>
                                @if ($itemsPreview)
                                    <span class="adm-cell-muted">{{ $itemsPreview }}@if ($items->count() > 2)…@endif</span>
                                @endif
                            </td>
                            <td>{{ number_format((float) $order->total_amount, 0, '.', ' ') }} грн</td>
                            <td>{{ $deliveryLabels[$order->delivery_method] ?? $order->delivery_method }}</td>
                            <td>{{ $paymentLabels[$order->payment_method] ?? $order->payment_method }}</td>
                            <td>@include('partials.admin-order-status', ['status' => $order->status])</td>
                            <td><a href="{{ url('/admin/orders/' . $order->id) }}" class="btn btn-dark btn-sm">Деталі</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="adm-empty">
            <p>У цього користувача ще немає замовлень.</p>
        </div>
    @endif
</section>

<div class="adm-grid-2">
    <section class="adm-panel">
        <div class="adm-panel-head"><h2>Промокоди</h2></div>
        @if ($promocodes->isNotEmpty())
            <ul class="adm-mini-list">
                @foreach ($promocodes as $promo)
                    <li>
                        <strong>{{ $promo->code }}</strong>
                        <span>{{ $promo->title }} · −{{ (int) $promo->discount_percent }}%</span>
                        <span class="adm-cell-muted">
                            @if ($promo->used_at)
                                Використано {{ $promo->used_at }}
                            @elseif ($promo->expires_at)
                                До {{ $promo->expires_at }}
                            @else
                                Активний
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="adm-empty compact"><p>Промокодів немає.</p></div>
        @endif
    </section>

    <section class="adm-panel">
        <div class="adm-panel-head"><h2>Історія бонусів</h2></div>
        @if ($bonusHistory->isNotEmpty())
            <ul class="adm-mini-list">
                @foreach ($bonusHistory as $row)
                    <li>
                        <strong>{{ ($row->points >= 0 ? '+' : '') . (int) $row->points }} б.</strong>
                        <span>{{ $row->description }}</span>
                        <span class="adm-cell-muted">{{ $row->created_at }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="adm-empty compact"><p>Нарахувань бонусів немає.</p></div>
        @endif
    </section>
</div>

@if ($reviews->isNotEmpty())
    <section class="adm-panel">
        <div class="adm-panel-head"><h2>Відгуки користувача</h2></div>
        <div class="adm-review-list">
            @foreach ($reviews as $review)
                <article class="adm-review-card">
                    <div class="adm-review-head">
                        <strong>{{ $review->product_name ?? 'Товар #' . $review->product_id }}</strong>
                        <span class="adm-review-rating">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= (int) $review->rating ? 'is-on' : '' }}">★</span>
                            @endfor
                        </span>
                    </div>
                    <p class="adm-review-text">{{ $review->comment }}</p>
                    <a href="{{ url('/product/' . $review->product_id) }}" class="adm-link" target="_blank">На сайті</a>
                </article>
            @endforeach
        </div>
    </section>
@endif

@if ($conversations->isNotEmpty())
    <section class="adm-panel">
        <div class="adm-panel-head"><h2>Діалоги з підтримкою</h2></div>
        <div class="adm-chat-list">
            @foreach ($conversations as $conversation)
                <a href="{{ url('/admin/messages/' . $conversation->id) }}" class="adm-chat-list-item">
                    <div class="adm-chat-list-main">
                        <strong>{{ $conversation->subject }}</strong>
                        <span class="adm-cell-muted">{{ $conversation->last_message_at ?? $conversation->created_at }}</span>
                    </div>
                    <span class="adm-badge adm-badge--{{ $conversation->status === 'open' ? 'success' : 'neutral' }}">
                        {{ $conversation->status === 'open' ? 'Відкрито' : 'Закрито' }}
                    </span>
                </a>
            @endforeach
        </div>
    </section>
@endif
@endsection
