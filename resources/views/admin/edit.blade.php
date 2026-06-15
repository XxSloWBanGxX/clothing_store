@extends('admin.layout')

@section('title', 'Редагувати товар')

@section('admin_content')

@php
    $sizeOptions = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'One Size'];
    $selectedSizes = (array) ($selectedSizes ?? []);
    $selectedColors = (array) ($selectedColors ?? []);
@endphp

@if (session('status'))
    <div class="alert-success">{{ session('status') }}</div>
@endif

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Редагувати товар</h2>
    </div>

    <form action="{{ url('/admin/products/' . $product->id) }}" method="POST" enctype="multipart/form-data" class="admin-form">
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
                        <option value="{{ $category->id }}" {{ (int) old('category_id', $product->category_id) === (int) $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
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
                <label for="old_price">Стара ціна (для знижки)</label>
                <input type="number" step="0.01" id="old_price" name="old_price" value="{{ old('old_price', $product->old_price) }}" placeholder="Необовʼязково">
                @error('old_price')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label for="stock">Кількість</label>
                <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}">
                @error('stock')<small class="form-error">{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="admin-media-grid">
            <div class="form-group">
                <label for="main_image">Головне фото</label>
                <div class="admin-upload-card">
                    <input type="file" id="main_image" name="main_image" accept=".jpg,.jpeg,.png,.webp" class="admin-hidden-file">
                    <label for="main_image" class="admin-upload-btn">Замінити головне фото</label>

                    <div class="admin-upload-preview single" id="mainImagePreview">
                        @if (!empty($product->image))
                            <div class="admin-preview-item large">
                                <img src="{{ asset('assets/images/products/' . $product->image) }}" alt="main">
                            </div>
                        @else
                            <div class="admin-empty-preview">Головне фото ще не додане</div>
                        @endif
                    </div>
                </div>
                @error('main_image')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label for="gallery_images">Нові фото в галерею</label>
                <div class="admin-upload-card">
                    <input type="file" id="gallery_images" name="gallery_images[]" multiple accept=".jpg,.jpeg,.png,.webp" class="admin-hidden-file">
                    <label for="gallery_images" class="admin-upload-btn secondary">Додати ще фото</label>

                    <div class="admin-upload-preview multi" id="galleryImagesPreviewNew">
                        <div class="admin-empty-preview">Тут з’являться нові фото, які ти додаєш</div>
                    </div>
                </div>
                @error('gallery_images.*')<small class="form-error">{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="form-group">
            <label>Поточна галерея</label>
            <div class="admin-current-gallery" id="currentGalleryBox">
                @forelse ($images as $img)
                    <div class="admin-current-gallery-item">
                        <input type="hidden" name="keep_gallery_images[]" value="{{ (int) $img->id }}" class="keep-gallery-image-input">

                        <div class="admin-preview-item">
                            <img src="{{ asset('assets/images/products/' . $img->image_path) }}" alt="gallery">
                        </div>

                        <button type="button" class="admin-gallery-remove-btn">Прибрати</button>
                    </div>
                @empty
                    <div class="admin-empty-preview gallery-empty-placeholder">Фото галереї ще не додані</div>
                @endforelse
            </div>
            <small class="admin-help-text">Нові фото додаються до старих. Непотрібні старі фото можна прибрати кнопкою.</small>
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
                            <input type="checkbox" name="sizes[]" value="{{ $size }}" {{ in_array($size, $selectedSizes, true) ? 'checked' : '' }}>
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
                            <input type="checkbox" name="colors[]" value="{{ $colorName }}" {{ in_array($colorName, $selectedColors, true) ? 'checked' : '' }}>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mainImageInput = document.getElementById('main_image');
    const mainImagePreview = document.getElementById('mainImagePreview');

    if (mainImageInput && mainImagePreview) {
        mainImageInput.addEventListener('change', function () {
            mainImagePreview.innerHTML = '';
            const file = this.files?.[0];
            if (!file) {
                mainImagePreview.innerHTML = '<div class="admin-empty-preview">Головне фото ще не додане</div>';
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
                galleryPreviewNew.innerHTML = '<div class="admin-empty-preview">Тут з’являться нові фото, які ти додаєш</div>';
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

    const currentGalleryBox = document.getElementById('currentGalleryBox');

    function ensureGalleryEmptyPlaceholder() {
        if (!currentGalleryBox) return;
        const items = currentGalleryBox.querySelectorAll('.admin-current-gallery-item');
        const existingPlaceholder = currentGalleryBox.querySelector('.gallery-empty-placeholder');
        if (items.length === 0 && !existingPlaceholder) {
            const empty = document.createElement('div');
            empty.className = 'admin-empty-preview gallery-empty-placeholder';
            empty.textContent = 'Фото галереї ще не додані';
            currentGalleryBox.appendChild(empty);
        }
        if (items.length > 0 && existingPlaceholder) {
            existingPlaceholder.remove();
        }
    }

    if (currentGalleryBox) {
        currentGalleryBox.addEventListener('click', function (e) {
            if (e.target.classList.contains('admin-gallery-remove-btn')) {
                const item = e.target.closest('.admin-current-gallery-item');
                if (!item) return;
                item.remove();
                ensureGalleryEmptyPlaceholder();
            }
        });
        ensureGalleryEmptyPlaceholder();
    }
});
</script>
@endsection
