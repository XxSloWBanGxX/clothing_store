@extends('admin.layout')

@section('title', 'Редагувати товар')

@section('admin_content')

@include('partials.admin-flash')

@php
    $sizeOptions = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'One Size'];
    $selectedSizes = (array) ($selectedSizes ?? []);
    $selectedColors = (array) ($selectedColors ?? []);
@endphp

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Редагувати товар</h2>
            <p>#{{ (int) $product->id }} · {{ $product->name }}</p>
        </div>
        <a href="{{ url('/product/' . $product->id) }}" class="btn btn-light btn-sm" target="_blank">На сайті</a>
    </div>

    <form action="{{ url('/admin/products/' . $product->id) }}" method="POST" enctype="multipart/form-data" class="admin-form" id="productForm">
        @csrf
        @method('PUT')

        <div class="admin-form-grid">
            <div class="form-group">
                <label for="name">Назва товару</label>
                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}">
                @error('name')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $product->slug) }}">
                @error('slug')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="category_id">Категорія</label>
                <select id="category_id" name="category_id">
                    <option value="">Оберіть категорію</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ (int) old('category_id', $product->category_id) === (int) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="price">Ціна</label>
                <input type="number" step="0.01" id="price" name="price" value="{{ old('price', $product->price) }}">
                @error('price')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="old_price">Стара ціна</label>
                <input type="number" step="0.01" id="old_price" name="old_price" value="{{ old('old_price', $product->old_price) }}" placeholder="Необовʼязково">
                @error('old_price')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="stock">Кількість</label>
                <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}">
                @error('stock')<small class="form-error">{{ $message }}</small>@enderror
            </div>
        </div>

        @include('partials.admin-product-media', [
            'mode' => 'edit',
            'product' => $product,
            'images' => $images,
        ])

        <div class="admin-ui-grid">
            <div class="admin-selector-card">
                <div class="admin-selector-head"><h3>Розміри</h3><p>Доступні розміри</p></div>
                <div class="admin-size-grid">
                    @foreach ($sizeOptions as $size)
                        <label class="admin-size-pill">
                            <input type="checkbox" name="sizes[]" value="{{ $size }}" {{ in_array($size, $selectedSizes, true) ? 'checked' : '' }}>
                            <span>{{ $size }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="admin-selector-card">
                <div class="admin-selector-head"><h3>Кольори</h3><p>Базові кольори</p></div>
                <div class="admin-color-grid">
                    @foreach ($baseColors as $colorName => $hex)
                        <label class="admin-color-pill">
                            <input type="checkbox" name="colors[]" value="{{ $colorName }}" {{ in_array($colorName, $selectedColors, true) ? 'checked' : '' }}>
                            <span><i class="admin-color-pill-dot" style="background: {{ $hex }};"></i>{{ $colorName }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Опис товару</label>
            <textarea id="description" name="description" rows="6">{{ old('description', $product->description) }}</textarea>
            @error('description')<small class="form-error">{{ $message }}</small>@enderror
        </div>

        <div class="filter-checkbox">
            <label>
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                Показувати як популярний товар
            </label>
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn btn-dark admin-save-btn">Оновити товар</button>
            <a href="{{ url('/admin/products') }}" class="btn btn-light">Назад</a>
        </div>
    </form>
</section>
@endsection

@section('admin_scripts')
<script src="{{ asset('assets/js/admin-product-media.js') }}"></script>
@endsection
