@extends('admin.layout')

@section('title', 'Промокоди')

@section('admin_content')
@include('partials.admin-flash')

@php
    $promoService = app(\App\Services\PromoService::class);
    $activeTab = $activeTab ?? 'create';
    $filter = $filter ?? 'all';
    $stats = $stats ?? ['total' => 0, 'active' => 0, 'scheduled' => 0, 'expired' => 0];
    $editId = (int) request('edit');
    $filters = [
        'all' => 'Усі',
        'active' => 'Активні',
        'scheduled' => 'Заплановані',
        'expired' => 'Завершені',
        'disabled' => 'Вимкнені',
    ];
@endphp

<section class="adm-kpi-grid adm-kpi-grid--4">
    <article class="adm-kpi"><span class="adm-kpi-label">Усього кодів</span><strong class="adm-kpi-value">{{ $stats['total'] }}</strong></article>
    <article class="adm-kpi adm-kpi--success"><span class="adm-kpi-label">Активні зараз</span><strong class="adm-kpi-value">{{ $stats['active'] }}</strong></article>
    <article class="adm-kpi adm-kpi--info"><span class="adm-kpi-label">Заплановані</span><strong class="adm-kpi-value">{{ $stats['scheduled'] }}</strong></article>
    <article class="adm-kpi adm-kpi--muted"><span class="adm-kpi-label">Завершені</span><strong class="adm-kpi-value">{{ $stats['expired'] }}</strong></article>
</section>

<div class="adm-marketing-shell">
    @include('partials.admin-marketing-nav', [
        'tabs' => [
            'create' => ['label' => 'Новий промокод', 'desc' => 'Створити код для checkout'],
            'list' => ['label' => 'Усі промокоди', 'desc' => $stats['total'] . ' кодів у базі'],
        ],
        'activeTab' => $activeTab,
        'baseUrl' => url('/admin/promocodes'),
        'previewUrl' => url('/checkout'),
        'previewLabel' => 'Сторінка checkout',
    ])

    <div class="adm-marketing-main">
        @if ($activeTab === 'create')
            <header class="adm-marketing-head">
                <div>
                    <h2>Створити промокод</h2>
                    <p>Клієнт вводить код при оформленні замовлення — знижка застосовується до суми кошика</p>
                </div>
            </header>

            <form action="{{ url('/admin/promocodes') }}" method="POST" class="adm-marketing-form">
                @csrf

                <div class="adm-settings-card">
                    <h3 class="adm-settings-card-title">Основне</h3>
                    <div class="admin-form-grid">
                        @include('partials.admin-settings-field', [
                            'name' => 'code',
                            'label' => 'Код промокоду',
                            'hint' => 'Латиниця та цифри, автоматично у верхньому регістрі',
                            'placeholder' => 'SUMMER20',
                            'value' => old('code'),
                            'inputId' => 'promo_code',
                        ])
                        @include('partials.admin-settings-field', [
                            'name' => 'title',
                            'label' => 'Назва для адмінки',
                            'hint' => 'Внутрішня назва — клієнт її не бачить',
                            'placeholder' => 'Літня акція 20%',
                            'value' => old('title'),
                        ])
                        @include('partials.admin-settings-field', [
                            'name' => 'discount_percent',
                            'label' => 'Знижка, %',
                            'type' => 'number',
                            'min' => 1,
                            'max' => 90,
                            'hint' => 'Від суми товарів у кошику',
                            'value' => old('discount_percent', 10),
                        ])
                    </div>
                </div>

                <div class="adm-settings-card">
                    <h3 class="adm-settings-card-title">Обмеження</h3>
                    <p class="adm-settings-card-lead">Необовʼязково — залиш порожнім, якщо обмежень немає</p>
                    <div class="admin-form-grid">
                        @include('partials.admin-settings-field', [
                            'name' => 'min_order_amount',
                            'label' => 'Мінімальна сума замовлення (грн)',
                            'type' => 'number',
                            'step' => '0.01',
                            'min' => 0,
                            'placeholder' => '500',
                            'value' => old('min_order_amount'),
                        ])
                        @include('partials.admin-settings-field', [
                            'name' => 'max_uses',
                            'label' => 'Максимум використань',
                            'type' => 'number',
                            'min' => 1,
                            'placeholder' => '100',
                            'value' => old('max_uses'),
                        ])
                    </div>
                </div>

                <div class="adm-settings-card">
                    <h3 class="adm-settings-card-title">Період дії</h3>
                    <p class="adm-settings-card-lead">Активація за серверним часом ({{ config('app.timezone') }})</p>
                    <div class="admin-form-grid">
                        @include('partials.admin-settings-field', [
                            'name' => 'starts_at',
                            'label' => 'Діє з',
                            'type' => 'datetime-local',
                            'hint' => 'Порожньо — одразу після створення',
                            'value' => old('starts_at'),
                        ])
                        @include('partials.admin-settings-field', [
                            'name' => 'expires_at',
                            'label' => 'Діє до',
                            'type' => 'datetime-local',
                            'hint' => 'Порожньо — без терміну',
                            'value' => old('expires_at'),
                        ])
                        @include('partials.admin-switch', [
                            'name' => 'is_active',
                            'label' => 'Промокод увімкнено',
                            'hint' => 'Працює автоматично за розкладом',
                            'checked' => true,
                        ])
                    </div>
                </div>

                <div class="adm-settings-savebar">
                    <div class="adm-settings-savebar-copy">
                        <strong>Готово?</strong>
                        <span>Після створення код одразу зʼявиться у списку</span>
                    </div>
                    <button type="submit" class="btn btn-dark">Створити промокод</button>
                </div>
            </form>
        @else
            <header class="adm-marketing-head">
                <div>
                    <h2>Усі промокоди</h2>
                    <p>Керуй кодами, переглядай використання та статус</p>
                </div>
            </header>

            <div class="adm-filter-tabs adm-filter-tabs--spaced">
                @foreach ($filters as $key => $label)
                    <a href="{{ url('/admin/promocodes?tab=list&filter=' . $key) }}" class="adm-filter-tab {{ $filter === $key ? 'is-active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>

            <div class="adm-marketing-list">
                @forelse ($filtered as $promo)
                    @php
                        $status = $promoService->promocodeStatus($promo);
                        $statusLabel = $promoService->promocodeStatusLabel($status);
                        $isOpen = $editId === (int) $promo->id;
                    @endphp
                    <article class="adm-marketing-item {{ $isOpen ? 'is-open' : '' }}" id="promo-{{ $promo->id }}">
                        <div class="adm-marketing-item-head">
                            <div class="adm-marketing-item-main">
                                <code class="adm-promo-code">{{ $promo->code }}</code>
                                <strong>{{ $promo->title }}</strong>
                                <span class="adm-marketing-item-meta">−{{ (int) $promo->discount_percent }}%</span>
                            </div>
                            <div class="adm-marketing-item-side">
                                @if ($status === 'active')
                                    <span class="adm-badge adm-badge--success">{{ $statusLabel }}</span>
                                @elseif ($status === 'scheduled')
                                    <span class="adm-badge adm-badge--info">{{ $statusLabel }}</span>
                                @else
                                    <span class="adm-badge adm-badge--neutral">{{ $statusLabel }}</span>
                                @endif
                                <button type="button" class="btn btn-light btn-sm adm-marketing-toggle" data-target="promo-edit-{{ $promo->id }}">{{ $isOpen ? 'Згорнути' : 'Редагувати' }}</button>
                            </div>
                        </div>

                        <div class="adm-marketing-item-details">
                            <div class="adm-marketing-detail">
                                <span>Використань</span>
                                <strong>{{ (int) $promo->uses_count }}@if($promo->max_uses) / {{ (int) $promo->max_uses }}@else / ∞@endif</strong>
                            </div>
                            @if ($promo->min_order_amount)
                                <div class="adm-marketing-detail">
                                    <span>Мін. сума</span>
                                    <strong>{{ number_format((float) $promo->min_order_amount, 0, '.', ' ') }} грн</strong>
                                </div>
                            @endif
                            <div class="adm-marketing-detail">
                                <span>Період</span>
                                <strong>
                                    {{ $promo->starts_at ? \Illuminate\Support\Carbon::parse($promo->starts_at)->format('d.m.Y H:i') : '—' }}
                                    →
                                    {{ $promo->expires_at ? \Illuminate\Support\Carbon::parse($promo->expires_at)->format('d.m.Y H:i') : '∞' }}
                                </strong>
                            </div>
                        </div>

                        <div class="adm-marketing-item-edit" id="promo-edit-{{ $promo->id }}" {{ $isOpen ? '' : 'hidden' }}>
                            <form action="{{ url('/admin/promocodes/' . $promo->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="admin-form-grid">
                                    @include('partials.admin-settings-field', ['name' => 'code', 'label' => 'Код', 'value' => $promo->code])
                                    @include('partials.admin-settings-field', ['name' => 'title', 'label' => 'Назва', 'value' => $promo->title])
                                    @include('partials.admin-settings-field', ['name' => 'discount_percent', 'label' => 'Знижка %', 'type' => 'number', 'min' => 1, 'max' => 90, 'value' => $promo->discount_percent])
                                    @include('partials.admin-settings-field', ['name' => 'min_order_amount', 'label' => 'Мін. сума', 'type' => 'number', 'step' => '0.01', 'value' => $promo->min_order_amount])
                                    @include('partials.admin-settings-field', ['name' => 'max_uses', 'label' => 'Макс. використань', 'type' => 'number', 'value' => $promo->max_uses])
                                    @include('partials.admin-settings-field', ['name' => 'starts_at', 'label' => 'Діє з', 'type' => 'datetime-local', 'value' => $promo->starts_at ? \Illuminate\Support\Carbon::parse($promo->starts_at)->format('Y-m-d\TH:i') : ''])
                                    @include('partials.admin-settings-field', ['name' => 'expires_at', 'label' => 'Діє до', 'type' => 'datetime-local', 'value' => $promo->expires_at ? \Illuminate\Support\Carbon::parse($promo->expires_at)->format('Y-m-d\TH:i') : ''])
                                    @include('partials.admin-switch', [
                                        'name' => 'is_active',
                                        'label' => 'Промокод увімкнено',
                                        'checked' => (bool) $promo->is_active,
                                    ])
                                </div>
                                <div class="adm-marketing-item-actions">
                                    <button type="submit" class="btn btn-dark btn-sm">Зберегти</button>
                                </div>
                            </form>
                            <form action="{{ url('/admin/promocodes/' . $promo->id) }}" method="POST" class="adm-marketing-delete-form" onsubmit="return confirm('Видалити промокод?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light btn-sm">Видалити</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="adm-marketing-empty">
                        <p>Немає промокодів за обраним фільтром.</p>
                        <a href="{{ url('/admin/promocodes?tab=create') }}" class="btn btn-dark btn-sm">Створити перший</a>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</div>
@endsection

@section('admin_scripts')
<script>
(function () {
    document.querySelectorAll('.adm-marketing-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var panel = document.getElementById(btn.getAttribute('data-target'));
            if (!panel) return;
            var open = panel.hidden;
            panel.hidden = !open;
            btn.textContent = open ? 'Згорнути' : 'Редагувати';
            btn.closest('.adm-marketing-item')?.classList.toggle('is-open', open);
        });
    });

    var codeInput = document.getElementById('promo_code');
    if (codeInput) {
        codeInput.addEventListener('input', function () {
            codeInput.value = codeInput.value.toUpperCase().replace(/[^A-Z0-9_-]/g, '');
        });
    }
})();
</script>
@endsection
