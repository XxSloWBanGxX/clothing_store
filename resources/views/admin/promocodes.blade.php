@extends('admin.layout')

@section('title', 'Промокоди')

@section('admin_content')
@include('partials.admin-flash')

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Створити промокод</h2>
            <p>Глобальні коди для всіх клієнтів на checkout</p>
        </div>
    </div>

    <form action="{{ url('/admin/promocodes') }}" method="POST" class="adm-form">
        @csrf
        <div class="admin-form-grid">
            <div class="form-group">
                <label for="code">Код</label>
                <input type="text" id="code" name="code" value="{{ old('code') }}" placeholder="SUMMER20">
                @error('code')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="title">Назва акції</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}">
                @error('title')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="discount_percent">Знижка %</label>
                <input type="number" min="1" max="90" id="discount_percent" name="discount_percent" value="{{ old('discount_percent', 10) }}">
            </div>
            <div class="form-group">
                <label for="min_order_amount">Мін. сума (грн)</label>
                <input type="number" min="0" step="0.01" id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount') }}">
            </div>
            <div class="form-group">
                <label for="max_uses">Макс. використань</label>
                <input type="number" min="1" id="max_uses" name="max_uses" value="{{ old('max_uses') }}">
            </div>
            <div class="form-group">
                <label for="expires_at">Діє до</label>
                <input type="date" id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
            </div>
            <div class="form-group adm-checkbox-row">
                <label class="adm-check">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span>Активний</span>
                </label>
            </div>
        </div>
        <div class="admin-form-actions">
            <button type="submit" class="btn btn-dark">Створити</button>
        </div>
    </form>
</section>

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Усі промокоди</h2>
            <p>{{ $promocodes->count() }} кодів</p>
        </div>
    </div>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Код</th>
                    <th>Назва</th>
                    <th>Знижка</th>
                    <th>Використань</th>
                    <th>Статус</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($promocodes as $promo)
                    <tr>
                        <td><strong>{{ $promo->code }}</strong></td>
                        <td>{{ $promo->title }}</td>
                        <td>{{ (int) $promo->discount_percent }}%</td>
                        <td>{{ (int) $promo->uses_count }}@if($promo->max_uses) / {{ (int) $promo->max_uses }}@endif</td>
                        <td>
                            @if ($promo->is_active)
                                <span class="adm-badge adm-badge--success">Активний</span>
                            @else
                                <span class="adm-badge adm-badge--neutral">Вимкнено</span>
                            @endif
                        </td>
                        <td>
                            <details class="adm-inline-edit">
                                <summary class="btn btn-light btn-sm">Редагувати</summary>
                                <form action="{{ url('/admin/promocodes/' . $promo->id) }}" method="POST" class="adm-inline-form">
                                    @csrf
                                    @method('PUT')
                                    <div class="admin-form-grid">
                                        <div class="form-group"><label>Код</label><input type="text" name="code" value="{{ $promo->code }}"></div>
                                        <div class="form-group"><label>Назва</label><input type="text" name="title" value="{{ $promo->title }}"></div>
                                        <div class="form-group"><label>%</label><input type="number" name="discount_percent" value="{{ $promo->discount_percent }}"></div>
                                        <div class="form-group"><label>Мін. сума</label><input type="number" step="0.01" name="min_order_amount" value="{{ $promo->min_order_amount }}"></div>
                                        <div class="form-group"><label>Макс.</label><input type="number" name="max_uses" value="{{ $promo->max_uses }}"></div>
                                        <div class="form-group"><label>До</label><input type="date" name="expires_at" value="{{ $promo->expires_at ? \Illuminate\Support\Carbon::parse($promo->expires_at)->format('Y-m-d') : '' }}"></div>
                                        <div class="form-group adm-checkbox-row">
                                            <label class="adm-check"><input type="checkbox" name="is_active" value="1" {{ $promo->is_active ? 'checked' : '' }}><span>Активний</span></label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-dark btn-sm">Зберегти</button>
                                </form>
                            </details>
                            <form action="{{ url('/admin/promocodes/' . $promo->id) }}" method="POST" class="adm-inline-delete" onsubmit="return confirm('Видалити промокод?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light btn-sm">Видалити</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">Промокодів ще немає.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
