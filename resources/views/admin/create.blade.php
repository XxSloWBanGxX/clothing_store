@extends('admin.layout')

@section('title', 'Додати товар')

@section('admin_content')

@php
    $sizeOptions = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'One Size'];
    $selectedOldSizes = (array) old('sizes', []);
    $selectedOldColors = (array) old('colors', []);
@endphp

<section class="admin-panel-box">
    <div class="admin-panel-head">
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
                        <option value="{{ $category->id }}" {{ (string) old('category_id') === (string) $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
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
                <label for="old_price">Стара ціна (для знижки)</label>
                <input type="number" step="0.01" id="old_price" name="old_price" value="{{ old('old_price') }}" placeholder="Необовʼязково">
                @error('old_price')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label for="stock">Кількість</label>
                <input type="number" id="stock" name="stock" value="{{ old('stock') }}">
                @error('stock')<small class="form-error">{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="admin-media-grid">
            <div class="form-group">
                <label for="main_image">Головне фото</label>
                <div class="admin-upload-card">
                    <input type="file" id="main_image" name="main_image" accept=".jpg,.jpeg,.png,.webp" class="admin-hidden-file">
                    <label for="main_image" class="admin-upload-btn">Вибрати головне фото</label>
                    <div class="admin-upload-preview single" id="mainImagePreview">
                        <div class="admin-empty-preview">Тут з’явиться головне фото</div>
                    </div>
                </div>
                @error('main_image')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label for="gallery_images">Фото галереї</label>
                <div class="admin-upload-card">
                    <input type="file" id="gallery_images" name="gallery_images[]" multiple accept=".jpg,.jpeg,.png,.webp" class="admin-hidden-file">
                    <label for="gallery_images" class="admin-upload-btn secondary">Додати фото в галерею</label>
                    <div class="admin-upload-preview multi" id="galleryImagesPreviewNew">
                        <div class="admin-empty-preview">Тут з’являться фото галереї</div>
                    </div>
                </div>
                @error('gallery_images.*')<small class="form-error">{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="admin-ui-grid">
            <div class="admin-selector-card">
                <div class="admin-selector-head">
                    <h3>Розміри</h3>
                    <p>Вибери доступні розміри</p>
                </div>

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
                <div class="admin-selector-head">
                    <h3>Кольори</h3>
                    <p>Вибери базові кольори товару</p>
                </div>

                <div class="admin-color-grid">
                    @foreach ($baseColors as $colorName => $hex)
                        <label class="admin-color-pill">
                            <input type="checkbox" name="colors[]" value="{{ $colorName }}" {{ in_array($colorName, $selectedOldColors, true) ? 'checked' : '' }}>
                            <span>
                                <i class="admin-color-pill-dot" style="background: {{ $hex }};"></i>
                                {{ $colorName }}
                            </span>
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
            <button type="submit" class="btn btn-dark admin-save-btn">Зберегти товар</button>
            <a href="{{ url('/admin/products') }}" class="btn btn-light">Скасувати</a>
        </div>
    </form>
</section>

@endsection

@section('admin_scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mainImageInput = document.getElementById('main_image');
    const mainImagePreview = document.getElementById('mainImagePreview');

    if (mainImageInput && mainImagePreview) {
        mainImageInput.addEventListener('change', function () {
            mainImagePreview.innerHTML = '';
            const file = this.files?.[0];
            if (!file) {
                mainImagePreview.innerHTML = '<div class="admin-empty-preview">Тут з’явиться головне фото</div>';
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                mainImagePreview.innerHTML = `<div class="admin-preview-item large"><img src="${e.target.result}" alt="preview"></div>`;
            };
            reader.readAsDataURL(file);
        });
    }

    const galleryInput = document.getElementById('gallery_images');
    const galleryPreviewNew = document.getElementById('galleryImagesPreviewNew');

    if (galleryInput && galleryPreviewNew) {
        galleryInput.addEventListener('change', function () {
            galleryPreviewNew.innerHTML = '';
            const files = Array.from(this.files || []);
            if (!files.length) {
                galleryPreviewNew.innerHTML = '<div class="admin-empty-preview">Тут з’являться фото галереї</div>';
                return;
            }
            files.forEach((file) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const item = document.createElement('div');
                    item.className = 'admin-preview-item';
                    item.innerHTML = `<img src="${e.target.result}" alt="preview">`;
                    galleryPreviewNew.appendChild(item);
                };
                reader.readAsDataURL(file);
            });
        });
    }
});
</script>
@endsection
