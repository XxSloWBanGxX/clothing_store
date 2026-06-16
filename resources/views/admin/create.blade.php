@extends('admin.layout')

@section('title', 'Додати товар')

@section('admin_content')

@include('partials.admin-flash')

@php
    $sizeOptions = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'One Size'];
    $selectedOldSizes = (array) old('sizes', []);
    $selectedOldColors = (array) old('colors', []);
@endphp

<section class="adm-panel">
    <div class="adm-panel-head">
        <h2>Додати товар</h2>
    </div>

    <form action="{{ url('/admin/products') }}" method="POST" enctype="multipart/form-data" class="admin-form">
        @csrf

        <div class="admin-form-grid">
            <div class="form-group">
                <label for="name">Назва товару</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}">
                @error('name')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="slug">Slug (необов’язково)</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug') }}">
                @error('slug')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="category_id">Категорія</label>
                <select id="category_id" name="category_id">
                    <option value="">Оберіть категорію</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ (string) old('category_id') === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="price">Ціна</label>
                <input type="number" step="0.01" id="price" name="price" value="{{ old('price') }}">
                @error('price')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="old_price">Стара ціна</label>
                <input type="number" step="0.01" id="old_price" name="old_price" value="{{ old('old_price') }}" placeholder="Необовʼязково">
                @error('old_price')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="stock">Кількість</label>
                <input type="number" id="stock" name="stock" value="{{ old('stock') }}">
                @error('stock')<small class="form-error">{{ $message }}</small>@enderror
            </div>
        </div>

        @include('partials.admin-product-media', ['mode' => 'create'])

        <div class="admin-ui-grid">
            <div class="admin-selector-card">
                <div class="admin-selector-head"><h3>Розміри</h3></div>
                <div class="admin-size-grid">
                    @foreach ($sizeOptions as $size)
                        <label class="admin-size-pill">
                            <input type="checkbox" name="sizes[]" value="{{ $size }}" {{ in_array($size, $selectedOldSizes, true) ? 'checked' : '' }}>
                            <span>{{ $size }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="admin-selector-card">
                <div class="admin-selector-head"><h3>Кольори</h3></div>
                <div class="admin-color-grid">
                    @foreach ($baseColors as $colorName => $hex)
                        <label class="admin-color-pill">
                            <input type="checkbox" name="colors[]" value="{{ $colorName }}" {{ in_array($colorName, $selectedOldColors, true) ? 'checked' : '' }}>
                            <span><i class="admin-color-pill-dot" style="background: {{ $hex }};"></i>{{ $colorName }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Опис товару</label>
            <textarea id="description" name="description" rows="6">{{ old('description') }}</textarea>
            @error('description')<small class="form-error">{{ $message }}</small>@enderror
        </div>

        <div class="filter-checkbox">
            <label>
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                Показувати як популярний товар
            </label>
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn btn-dark">Зберегти товар</button>
            <a href="{{ url('/admin/products') }}" class="btn btn-light">Скасувати</a>
        </div>
    </form>
</section>
@endsection

@section('admin_scripts')
<script src="{{ asset('assets/js/admin-product-media.js') }}"></script>
@endsection
