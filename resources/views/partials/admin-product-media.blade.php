@php
    $mode = $mode ?? 'create';
    $product = $product ?? null;
    $images = $images ?? collect();
    $mainImage = $product->image ?? null;
    $initialMainSource = 'current';

    if ($mode === 'create') {
        $initialMainSource = 'new';
    } elseif (empty($mainImage)) {
        $initialMainSource = 'none';
    }
@endphp

<div class="adm-media-manager" data-media-manager data-mode="{{ $mode }}">
    <input type="hidden" name="main_image_source" id="mainImageSource" value="{{ $initialMainSource }}">
    <input type="hidden" name="gallery_order" id="galleryOrderInput" value="">
    <input type="file" name="main_image" id="mainImageFile" accept=".jpg,.jpeg,.png,.webp" class="admin-hidden-file" hidden>
    <input type="file" name="gallery_images[]" id="galleryImagesFile" accept=".jpg,.jpeg,.png,.webp" multiple class="admin-hidden-file" hidden>

    <div class="adm-media-head">
        <div>
            <h3>Фото товару</h3>
            <p>Перетягни файли, обери головне фото зірочкою, змінюй порядок перетягуванням</p>
        </div>
        <label for="galleryImagesFile" class="btn btn-dark btn-sm">+ Додати фото</label>
    </div>

    <div class="adm-media-dropzone" id="mediaDropzone" tabindex="0">
        <div class="adm-media-dropzone-inner">
            <span class="adm-media-dropzone-icon">📷</span>
            <strong>Перетягни фото сюди</strong>
            <span>JPG, PNG, WEBP · можна одразу багато файлів</span>
        </div>
    </div>

    <div class="adm-media-grid" id="mediaGrid">
        @if ($mode === 'edit' && ! empty($mainImage))
            <article
                class="adm-media-item is-main"
                draggable="true"
                data-type="current-main"
                data-path="{{ $mainImage }}"
            >
                <img src="{{ asset('assets/images/products/' . $mainImage) }}" alt="">
                <span class="adm-media-badge">Головне</span>
                <div class="adm-media-actions">
                    <button type="button" class="adm-media-btn is-active" data-action="set-main" title="Головне">★</button>
                    <button type="button" class="adm-media-btn" data-action="remove" title="Видалити">×</button>
                </div>
            </article>
        @endif

        @if ($mode === 'edit')
            @foreach ($images as $img)
                <article
                    class="adm-media-item"
                    draggable="true"
                    data-type="gallery"
                    data-id="{{ (int) $img->id }}"
                    data-path="{{ $img->image_path }}"
                >
                    <input type="hidden" name="keep_gallery_images[]" value="{{ (int) $img->id }}" class="keep-gallery-input">
                    <img src="{{ asset('assets/images/products/' . $img->image_path) }}" alt="">
                    <div class="adm-media-actions">
                        <button type="button" class="adm-media-btn" data-action="set-main" title="Зробити головним">★</button>
                        <button type="button" class="adm-media-btn" data-action="remove" title="Прибрати">×</button>
                    </div>
                </article>
            @endforeach
        @endif
    </div>

    <p class="adm-media-hint" id="mediaEmptyHint" @if ($mode === 'edit' && (! empty($mainImage) || $images->count())) hidden @endif>
        Фото ще не додані. Завантаж або перетягни зображення в зону вище.
    </p>
</div>

@error('main_image')<small class="form-error">{{ $message }}</small>@enderror
@error('gallery_images.*')<small class="form-error">{{ $message }}</small>@enderror
