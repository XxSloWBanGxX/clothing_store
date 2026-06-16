@extends('admin.layout')

@section('title', 'Акції та знижки')

@section('admin_content')
@include('partials.admin-flash')

@php
    $pricing = app(\App\Services\PricingService::class);
    $serverNow = $pricing->now();
@endphp

<section class="adm-panel adm-panel--info">
    <div class="adm-panel-head">
        <div>
            <h2>Серверний час</h2>
            <p>Знижки автоматично вмикаються та вимикаються за цим часом ({{ config('app.timezone') }})</p>
        </div>
        <div class="adm-server-clock" id="admServerClock" data-server-now="{{ $serverNow->toIso8601String() }}">
            <strong>{{ $serverNow->format('d.m.Y H:i:s') }}</strong>
        </div>
    </div>
</section>

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Створити акцію</h2>
            <p>Знижка на товари за розкладом — без ручного редагування цін</p>
        </div>
    </div>

    <form action="{{ url('/admin/sales') }}" method="POST" class="adm-form" id="saleCreateForm">
        @csrf
        <div class="admin-form-grid">
            <div class="form-group form-group--wide">
                <label for="title">Назва акції</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Літній розпродаж">
                @error('title')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group form-group--wide">
                <label for="description">Опис</label>
                <textarea id="description" name="description" rows="2" placeholder="Короткий опис для сторінки знижок">{{ old('description') }}</textarea>
            </div>
            <div class="form-group">
                <label for="discount_percent">Знижка %</label>
                <input type="number" min="1" max="90" id="discount_percent" name="discount_percent" value="{{ old('discount_percent', 15) }}">
                @error('discount_percent')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="scope">Область дії</label>
                <select id="scope" name="scope">
                    <option value="all" {{ old('scope', 'all') === 'all' ? 'selected' : '' }}>Усі товари</option>
                    <option value="category" {{ old('scope') === 'category' ? 'selected' : '' }}>Категорія</option>
                    <option value="products" {{ old('scope') === 'products' ? 'selected' : '' }}>Окремі товари</option>
                </select>
            </div>
            <div class="form-group sale-scope-field sale-scope-category" hidden>
                <label for="category_id">Категорія</label>
                <select id="category_id" name="category_id">
                    <option value="">— Обери —</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string) old('category_id') === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group form-group--wide sale-scope-field sale-scope-products" hidden>
                <label>Товари</label>
                <div class="adm-product-picker">
                    @foreach ($products as $product)
                        <label class="adm-check adm-check--inline">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" {{ in_array($product->id, old('product_ids', [])) ? 'checked' : '' }}>
                            <span>{{ $product->name }} ({{ number_format((float) $product->price, 0, '.', ' ') }} грн)</span>
                        </label>
                    @endforeach
                </div>
                @error('product_ids')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="starts_at">Початок</label>
                <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at') }}">
                <small class="form-hint">Порожньо = одразу</small>
            </div>
            <div class="form-group">
                <label for="ends_at">Завершення</label>
                <input type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at') }}">
                <small class="form-hint">Порожньо = без обмеження</small>
                @error('ends_at')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group form-group--wide">
                <label for="banner_text">Текст банера на сайті</label>
                <input type="text" id="banner_text" name="banner_text" value="{{ old('banner_text') }}" placeholder="Літній розпродаж — знижки до 30%">
            </div>
            <div class="form-group adm-checkbox-row">
                <label class="adm-check">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span>Активна (за розкладом)</span>
                </label>
            </div>
            <div class="form-group adm-checkbox-row">
                <label class="adm-check">
                    <input type="checkbox" name="show_banner" value="1" {{ old('show_banner') ? 'checked' : '' }}>
                    <span>Показувати банер на сайті</span>
                </label>
            </div>
        </div>
        <div class="admin-form-actions">
            <button type="submit" class="btn btn-dark">Створити акцію</button>
        </div>
    </form>
</section>

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Усі акції</h2>
            <p>{{ $sales->count() }} акцій</p>
        </div>
    </div>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Назва</th>
                    <th>Знижка</th>
                    <th>Область</th>
                    <th>Період</th>
                    <th>Статус</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    @php
                        $status = $pricing->saleStatus($sale);
                        $statusLabel = $pricing->saleStatusLabel($status);
                        $scopeLabel = match ($sale->scope) {
                            'category' => 'Категорія',
                            'products' => count($saleProducts[$sale->id] ?? []) . ' товар(ів)',
                            default => 'Усі товари',
                        };
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $sale->title }}</strong>
                            @if ($sale->show_banner)
                                <span class="adm-badge adm-badge--info">Банер</span>
                            @endif
                        </td>
                        <td>{{ (int) $sale->discount_percent }}%</td>
                        <td>{{ $scopeLabel }}</td>
                        <td class="adm-cell-muted">
                            @if ($sale->starts_at)
                                {{ \Illuminate\Support\Carbon::parse($sale->starts_at)->format('d.m.Y H:i') }}
                            @else
                                —
                            @endif
                            →
                            @if ($sale->ends_at)
                                {{ \Illuminate\Support\Carbon::parse($sale->ends_at)->format('d.m.Y H:i') }}
                            @else
                                ∞
                            @endif
                        </td>
                        <td>
                            @if ($status === 'active')
                                <span class="adm-badge adm-badge--success">{{ $statusLabel }}</span>
                            @elseif ($status === 'scheduled')
                                <span class="adm-badge adm-badge--info">{{ $statusLabel }}</span>
                            @elseif ($status === 'expired')
                                <span class="adm-badge adm-badge--neutral">{{ $statusLabel }}</span>
                            @else
                                <span class="adm-badge adm-badge--neutral">{{ $statusLabel }}</span>
                            @endif
                        </td>
                        <td>
                            <details class="adm-inline-edit">
                                <summary class="btn btn-light btn-sm">Редагувати</summary>
                                <form action="{{ url('/admin/sales/' . $sale->id) }}" method="POST" class="adm-inline-form sale-edit-form">
                                    @csrf
                                    @method('PUT')
                                    <div class="admin-form-grid">
                                        <div class="form-group form-group--wide">
                                            <label>Назва</label>
                                            <input type="text" name="title" value="{{ $sale->title }}">
                                        </div>
                                        <div class="form-group form-group--wide">
                                            <label>Опис</label>
                                            <textarea name="description" rows="2">{{ $sale->description }}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>%</label>
                                            <input type="number" name="discount_percent" min="1" max="90" value="{{ $sale->discount_percent }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Область</label>
                                            <select name="scope" class="sale-scope-select">
                                                <option value="all" {{ $sale->scope === 'all' ? 'selected' : '' }}>Усі</option>
                                                <option value="category" {{ $sale->scope === 'category' ? 'selected' : '' }}>Категорія</option>
                                                <option value="products" {{ $sale->scope === 'products' ? 'selected' : '' }}>Товари</option>
                                            </select>
                                        </div>
                                        <div class="form-group sale-scope-field sale-scope-category" {{ $sale->scope === 'category' ? '' : 'hidden' }}>
                                            <label>Категорія</label>
                                            <select name="category_id">
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat->id }}" {{ (int) $sale->category_id === (int) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group form-group--wide sale-scope-field sale-scope-products" {{ $sale->scope === 'products' ? '' : 'hidden' }}>
                                            <label>Товари</label>
                                            <div class="adm-product-picker">
                                                @foreach ($products as $product)
                                                    <label class="adm-check adm-check--inline">
                                                        <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" {{ in_array($product->id, $saleProducts[$sale->id] ?? []) ? 'checked' : '' }}>
                                                        <span>{{ $product->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Початок</label>
                                            <input type="datetime-local" name="starts_at" value="{{ $sale->starts_at ? \Illuminate\Support\Carbon::parse($sale->starts_at)->format('Y-m-d\TH:i') : '' }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Кінець</label>
                                            <input type="datetime-local" name="ends_at" value="{{ $sale->ends_at ? \Illuminate\Support\Carbon::parse($sale->ends_at)->format('Y-m-d\TH:i') : '' }}">
                                        </div>
                                        <div class="form-group form-group--wide">
                                            <label>Банер</label>
                                            <input type="text" name="banner_text" value="{{ $sale->banner_text }}">
                                        </div>
                                        <div class="form-group adm-checkbox-row">
                                            <label class="adm-check"><input type="checkbox" name="is_active" value="1" {{ $sale->is_active ? 'checked' : '' }}><span>Активна</span></label>
                                        </div>
                                        <div class="form-group adm-checkbox-row">
                                            <label class="adm-check"><input type="checkbox" name="show_banner" value="1" {{ $sale->show_banner ? 'checked' : '' }}><span>Банер</span></label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-dark btn-sm">Зберегти</button>
                                </form>
                            </details>
                            <form action="{{ url('/admin/sales/' . $sale->id) }}" method="POST" class="adm-inline-delete" onsubmit="return confirm('Видалити акцію?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light btn-sm">Видалити</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">Акцій ще немає. Створи першу знижку вище.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection

@section('admin_scripts')
<script>
(function () {
    function toggleScopeFields(root, scope) {
        root.querySelectorAll('.sale-scope-category').forEach(el => el.hidden = scope !== 'category');
        root.querySelectorAll('.sale-scope-products').forEach(el => el.hidden = scope !== 'products');
    }

    document.querySelectorAll('#saleCreateForm, .sale-edit-form').forEach(form => {
        const scopeSelect = form.querySelector('[name="scope"]');
        if (!scopeSelect) return;
        const update = () => toggleScopeFields(form, scopeSelect.value);
        scopeSelect.addEventListener('change', update);
        update();
    });

    const clock = document.getElementById('admServerClock');
    if (clock) {
        let serverTime = new Date(clock.dataset.serverNow);
        setInterval(() => {
            serverTime = new Date(serverTime.getTime() + 1000);
            const pad = n => String(n).padStart(2, '0');
            clock.querySelector('strong').textContent =
                pad(serverTime.getDate()) + '.' + pad(serverTime.getMonth() + 1) + '.' + serverTime.getFullYear() +
                ' ' + pad(serverTime.getHours()) + ':' + pad(serverTime.getMinutes()) + ':' + pad(serverTime.getSeconds());
        }, 1000);
    }
})();
</script>
@endsection
