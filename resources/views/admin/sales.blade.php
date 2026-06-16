@extends('admin.layout')

@section('title', 'Акції та знижки')

@section('admin_content')
@include('partials.admin-flash')

@php
    $pricing = app(\App\Services\PricingService::class);
    $activeTab = $activeTab ?? 'create';
    $filter = $filter ?? 'all';
    $stats = $stats ?? ['total' => 0, 'active' => 0, 'scheduled' => 0, 'expired' => 0];
    $serverNow = $serverNow ?? $pricing->now();
    $editId = (int) request('edit');
    $filters = [
        'all' => 'Усі',
        'active' => 'Активні',
        'scheduled' => 'Заплановані',
        'expired' => 'Завершені',
        'disabled' => 'Вимкнені',
    ];
@endphp

<section class="adm-panel adm-panel--info adm-panel--compact">
    <div class="adm-panel-head">
        <div>
            <h2>Серверний час</h2>
            <p>Акції вмикаються та вимикаються автоматично ({{ config('app.timezone') }})</p>
        </div>
        <div class="adm-server-clock" id="admServerClock" data-server-now="{{ $serverNow->toIso8601String() }}">
            <strong>{{ $serverNow->format('d.m.Y H:i:s') }}</strong>
        </div>
    </div>
</section>

<section class="adm-kpi-grid adm-kpi-grid--4">
    <article class="adm-kpi"><span class="adm-kpi-label">Усього акцій</span><strong class="adm-kpi-value">{{ $stats['total'] }}</strong></article>
    <article class="adm-kpi adm-kpi--success"><span class="adm-kpi-label">Активні зараз</span><strong class="adm-kpi-value">{{ $stats['active'] }}</strong></article>
    <article class="adm-kpi adm-kpi--info"><span class="adm-kpi-label">Заплановані</span><strong class="adm-kpi-value">{{ $stats['scheduled'] }}</strong></article>
    <article class="adm-kpi adm-kpi--muted"><span class="adm-kpi-label">Завершені</span><strong class="adm-kpi-value">{{ $stats['expired'] }}</strong></article>
</section>

<div class="adm-marketing-shell">
    @include('partials.admin-marketing-nav', [
        'tabs' => [
            'create' => ['label' => 'Нова акція', 'desc' => 'Знижка на товари за датами'],
            'list' => ['label' => 'Усі акції', 'desc' => $stats['total'] . ' акцій у базі'],
        ],
        'activeTab' => $activeTab,
        'baseUrl' => url('/admin/sales'),
        'previewUrl' => url('/sale'),
        'previewLabel' => 'Сторінка знижок',
    ])

    <div class="adm-marketing-main">
        @if ($activeTab === 'create')
            <header class="adm-marketing-head">
                <div>
                    <h2>Створити акцію</h2>
                    <p>Ціни на товари змінюються автоматично — не потрібно редагувати кожен товар вручну</p>
                </div>
            </header>

            <form action="{{ url('/admin/sales') }}" method="POST" class="adm-marketing-form" id="saleCreateForm">
                @csrf

                <div class="adm-settings-card">
                    <h3 class="adm-settings-card-title">Основне</h3>
                    <div class="admin-form-grid">
                        @include('partials.admin-settings-field', [
                            'name' => 'title',
                            'label' => 'Назва акції',
                            'hint' => 'Відображається на сторінці /sale',
                            'placeholder' => 'Літній розпродаж',
                            'value' => old('title'),
                            'wide' => true,
                        ])
                        @include('partials.admin-settings-field', [
                            'name' => 'description',
                            'label' => 'Опис',
                            'type' => 'textarea',
                            'rows' => 2,
                            'placeholder' => 'Короткий опис для покупців',
                            'value' => old('description'),
                            'wide' => true,
                        ])
                        @include('partials.admin-settings-field', [
                            'name' => 'discount_percent',
                            'label' => 'Знижка, %',
                            'type' => 'number',
                            'min' => 1,
                            'max' => 90,
                            'value' => old('discount_percent', 15),
                        ])
                    </div>
                </div>

                <div class="adm-settings-card">
                    <h3 class="adm-settings-card-title">На що поширюється</h3>
                    <div class="admin-form-grid">
                        <div class="form-group">
                            <div class="adm-field-head">
                                <label for="field_scope">Область дії</label>
                                <span class="adm-field-hint">Обери, які товари отримають знижку</span>
                            </div>
                            <select id="field_scope" name="scope">
                                <option value="all" {{ old('scope', 'all') === 'all' ? 'selected' : '' }}>Усі товари</option>
                                <option value="category" {{ old('scope') === 'category' ? 'selected' : '' }}>Одна категорія</option>
                                <option value="products" {{ old('scope') === 'products' ? 'selected' : '' }}>Окремі товари</option>
                            </select>
                        </div>
                        <div class="form-group sale-scope-field sale-scope-category adm-grid-full" hidden>
                            <div class="adm-field-head">
                                <label for="field_category_id">Категорія</label>
                            </div>
                            <select id="field_category_id" name="category_id">
                                <option value="">— Обери категорію —</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (string) old('category_id') === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<small class="form-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group sale-scope-field sale-scope-products adm-grid-full" hidden>
                            <div class="adm-field-head">
                                <label>Товари для акції</label>
                                <span class="adm-field-hint">Пошук і фільтр по категорії — зручно навіть при сотнях товарів</span>
                            </div>
                            @include('partials.admin-product-picker', [
                                'products' => $products,
                                'selected' => old('product_ids', []),
                            ])
                            @error('product_ids')<small class="form-error">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>

                <div class="adm-settings-card">
                    <h3 class="adm-settings-card-title">Розклад</h3>
                    <div class="admin-form-grid">
                        @include('partials.admin-settings-field', [
                            'name' => 'starts_at',
                            'label' => 'Початок',
                            'type' => 'datetime-local',
                            'hint' => 'Порожньо — одразу',
                            'value' => old('starts_at'),
                        ])
                        @include('partials.admin-settings-field', [
                            'name' => 'ends_at',
                            'label' => 'Завершення',
                            'type' => 'datetime-local',
                            'hint' => 'Порожньо — без обмеження',
                            'value' => old('ends_at'),
                        ])
                    </div>
                </div>

                <div class="adm-settings-card">
                    <h3 class="adm-settings-card-title">Банер на сайті</h3>
                    <p class="adm-settings-card-lead">Тонка смуга зверху всіх сторінок — опційно</p>
                    <div class="admin-form-grid">
                        @include('partials.admin-settings-field', [
                            'name' => 'banner_text',
                            'label' => 'Текст банера',
                            'placeholder' => 'Літній розпродаж — знижки до 30%',
                            'value' => old('banner_text'),
                            'wide' => true,
                        ])
                        @include('partials.admin-switch', [
                            'name' => 'show_banner',
                            'label' => 'Показувати банер на сайті',
                            'hint' => 'Тонка смуга зверху всіх сторінок',
                            'checked' => (bool) old('show_banner'),
                        ])
                        @include('partials.admin-switch', [
                            'name' => 'is_active',
                            'label' => 'Акція увімкнена',
                            'hint' => 'Працює автоматично за розкладом',
                            'checked' => old('is_active', true),
                        ])
                    </div>
                </div>

                <div class="adm-settings-savebar">
                    <div class="adm-settings-savebar-copy">
                        <strong>Створити акцію</strong>
                        <span>Знижки зʼявляться на /sale та в каталозі автоматично</span>
                    </div>
                    <button type="submit" class="btn btn-dark">Створити</button>
                </div>
            </form>
        @else
            <header class="adm-marketing-head">
                <div>
                    <h2>Усі акції</h2>
                    <p>Активні знижки на товари — окремо від промокодів на checkout</p>
                </div>
            </header>

            <div class="adm-filter-tabs adm-filter-tabs--spaced">
                @foreach ($filters as $key => $label)
                    <a href="{{ url('/admin/sales?tab=list&filter=' . $key) }}" class="adm-filter-tab {{ $filter === $key ? 'is-active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>

            <div class="adm-marketing-list">
                @forelse ($filtered as $sale)
                    @php
                        $status = $pricing->saleStatus($sale);
                        $statusLabel = $pricing->saleStatusLabel($status);
                        $scopeLabel = match ($sale->scope) {
                            'category' => 'Категорія: ' . ($categoryNames[$sale->category_id] ?? '—'),
                            'products' => count($saleProducts[$sale->id] ?? []) . ' товар(ів)',
                            default => 'Усі товари',
                        };
                        $isOpen = $editId === (int) $sale->id;
                    @endphp
                    <article class="adm-marketing-item {{ $isOpen ? 'is-open' : '' }}" id="sale-{{ $sale->id }}">
                        <div class="adm-marketing-item-head">
                            <div class="adm-marketing-item-main">
                                <strong>{{ $sale->title }}</strong>
                                <span class="adm-marketing-item-meta">−{{ (int) $sale->discount_percent }}%</span>
                                @if ($sale->show_banner)
                                    <span class="adm-badge adm-badge--info">Банер</span>
                                @endif
                            </div>
                            <div class="adm-marketing-item-side">
                                @if ($status === 'active')
                                    <span class="adm-badge adm-badge--success">{{ $statusLabel }}</span>
                                @elseif ($status === 'scheduled')
                                    <span class="adm-badge adm-badge--info">{{ $statusLabel }}</span>
                                @else
                                    <span class="adm-badge adm-badge--neutral">{{ $statusLabel }}</span>
                                @endif
                                <button type="button" class="btn btn-light btn-sm adm-marketing-toggle" data-target="sale-edit-{{ $sale->id }}">{{ $isOpen ? 'Згорнути' : 'Редагувати' }}</button>
                            </div>
                        </div>

                        @if ($sale->description)
                            <p class="adm-marketing-item-desc">{{ $sale->description }}</p>
                        @endif

                        <div class="adm-marketing-item-details">
                            <div class="adm-marketing-detail"><span>Область</span><strong>{{ $scopeLabel }}</strong></div>
                            <div class="adm-marketing-detail">
                                <span>Період</span>
                                <strong>
                                    {{ $sale->starts_at ? \Illuminate\Support\Carbon::parse($sale->starts_at)->format('d.m.Y H:i') : '—' }}
                                    →
                                    {{ $sale->ends_at ? \Illuminate\Support\Carbon::parse($sale->ends_at)->format('d.m.Y H:i') : '∞' }}
                                </strong>
                            </div>
                        </div>

                        <div class="adm-marketing-item-edit" id="sale-edit-{{ $sale->id }}" {{ $isOpen ? '' : 'hidden' }}>
                            <form action="{{ url('/admin/sales/' . $sale->id) }}" method="POST" class="sale-edit-form">
                                @csrf
                                @method('PUT')
                                <div class="admin-form-grid">
                                    @include('partials.admin-settings-field', ['name' => 'title', 'label' => 'Назва', 'value' => $sale->title, 'wide' => true])
                                    @include('partials.admin-settings-field', ['name' => 'description', 'label' => 'Опис', 'type' => 'textarea', 'rows' => 2, 'value' => $sale->description, 'wide' => true])
                                    @include('partials.admin-settings-field', ['name' => 'discount_percent', 'label' => 'Знижка %', 'type' => 'number', 'min' => 1, 'max' => 90, 'value' => $sale->discount_percent])
                                    <div class="form-group">
                                        <div class="adm-field-head"><label>Область</label></div>
                                        <select name="scope">
                                            <option value="all" {{ $sale->scope === 'all' ? 'selected' : '' }}>Усі товари</option>
                                            <option value="category" {{ $sale->scope === 'category' ? 'selected' : '' }}>Категорія</option>
                                            <option value="products" {{ $sale->scope === 'products' ? 'selected' : '' }}>Окремі товари</option>
                                        </select>
                                    </div>
                                    <div class="form-group sale-scope-field sale-scope-category adm-grid-full" {{ $sale->scope === 'category' ? '' : 'hidden' }}>
                                        <div class="adm-field-head"><label>Категорія</label></div>
                                        <select name="category_id">
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ (int) $sale->category_id === (int) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group sale-scope-field sale-scope-products adm-grid-full" {{ $sale->scope === 'products' ? '' : 'hidden' }}>
                                        <div class="adm-field-head"><label>Товари</label></div>
                                        @include('partials.admin-product-picker', [
                                            'products' => $products,
                                            'selected' => $saleProducts[$sale->id] ?? [],
                                        ])
                                    </div>
                                    @include('partials.admin-settings-field', ['name' => 'starts_at', 'label' => 'Початок', 'type' => 'datetime-local', 'value' => $sale->starts_at ? \Illuminate\Support\Carbon::parse($sale->starts_at)->format('Y-m-d\TH:i') : ''])
                                    @include('partials.admin-settings-field', ['name' => 'ends_at', 'label' => 'Кінець', 'type' => 'datetime-local', 'value' => $sale->ends_at ? \Illuminate\Support\Carbon::parse($sale->ends_at)->format('Y-m-d\TH:i') : ''])
                                    @include('partials.admin-settings-field', ['name' => 'banner_text', 'label' => 'Текст банера', 'value' => $sale->banner_text, 'wide' => true])
                                    @include('partials.admin-switch', [
                                        'name' => 'is_active',
                                        'label' => 'Акція увімкнена',
                                        'checked' => (bool) $sale->is_active,
                                    ])
                                    @include('partials.admin-switch', [
                                        'name' => 'show_banner',
                                        'label' => 'Банер на сайті',
                                        'checked' => (bool) $sale->show_banner,
                                    ])
                                </div>
                                <div class="adm-marketing-item-actions">
                                    <button type="submit" class="btn btn-dark btn-sm">Зберегти</button>
                                </div>
                            </form>
                            <form action="{{ url('/admin/sales/' . $sale->id) }}" method="POST" class="adm-marketing-delete-form" onsubmit="return confirm('Видалити акцію?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light btn-sm">Видалити</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="adm-marketing-empty">
                        <p>Немає акцій за обраним фільтром.</p>
                        <a href="{{ url('/admin/sales?tab=create') }}" class="btn btn-dark btn-sm">Створити першу</a>
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
    function toggleScopeFields(root, scope) {
        root.querySelectorAll('.sale-scope-category').forEach(function (el) { el.hidden = scope !== 'category'; });
        root.querySelectorAll('.sale-scope-products').forEach(function (el) { el.hidden = scope !== 'products'; });
    }

    document.querySelectorAll('#saleCreateForm, .sale-edit-form').forEach(function (form) {
        var scopeSelect = form.querySelector('[name="scope"]');
        if (scopeSelect) {
            var update = function () {
                toggleScopeFields(form, scopeSelect.value);
                form.querySelectorAll('[data-product-picker]').forEach(function (picker) {
                    var searchInput = picker.querySelector('[data-picker-search]');
                    if (searchInput) {
                        searchInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
            };
            scopeSelect.addEventListener('change', update);
            update();
        }
    });

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

    var clock = document.getElementById('admServerClock');
    if (clock) {
        var serverTime = new Date(clock.dataset.serverNow);
        setInterval(function () {
            serverTime = new Date(serverTime.getTime() + 1000);
            var pad = function (n) { return String(n).padStart(2, '0'); };
            clock.querySelector('strong').textContent =
                pad(serverTime.getDate()) + '.' + pad(serverTime.getMonth() + 1) + '.' + serverTime.getFullYear() +
                ' ' + pad(serverTime.getHours()) + ':' + pad(serverTime.getMinutes()) + ':' + pad(serverTime.getSeconds());
        }, 1000);
    }
})();
</script>
@endsection
