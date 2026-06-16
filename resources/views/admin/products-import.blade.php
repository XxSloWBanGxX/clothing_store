@extends('admin.layout')

@section('title', 'Масовий імпорт')

@section('admin_content')
@include('partials.admin-flash')

@if (! empty(session('importErrors')))
    <div class="adm-panel adm-import-errors">
        <h3>Примітки під час імпорту</h3>
        <ul>
            @foreach (session('importErrors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Масовий імпорт товарів</h2>
            <p>CSV з даними + ZIP з фото — один рядок = один товар</p>
        </div>
        <a href="{{ url('/admin/products/import/template') }}" class="btn btn-light btn-sm">Завантажити шаблон CSV</a>
    </div>

    <div class="adm-settings-card adm-settings-card--info adm-import-guide">
        <h3 class="adm-settings-card-title">Як завантажити багато товарів</h3>
        <ol class="adm-info-list adm-info-list--ordered">
            <li><strong>Підготуй фото</strong> — jpg/png/webp, назви латиницею без пробілів (<code>hoodie-black.jpg</code>).</li>
            <li><strong>Запакуй у ZIP</strong> — усі фото в одну папку (можна в підпапках).</li>
            <li><strong>Заповни CSV</strong> — у колонках <code>main_image</code> і <code>gallery_images</code> вкажи імена файлів. Галерея через <code>|</code>.</li>
            <li><strong>category_slug</strong> — лише зі списку нижче (точна відповідність).</li>
            <li><strong>Завантаж обидва файли</strong> тут або через консоль для дуже великих архівів.</li>
        </ol>
        <p class="adm-import-cli-hint">
            Для сотень товарів / великого ZIP:
            <code>php artisan shop:import-products import-examples/products.csv --images=import-examples/photos.zip</code>
        </p>
    </div>

    <form action="{{ url('/admin/products/import') }}" method="POST" enctype="multipart/form-data" class="adm-form adm-import-form">
        @csrf

        <div class="adm-import-grid">
            <div class="adm-import-card">
                <span class="adm-import-step">1</span>
                <h3>CSV файл</h3>
                <p>Excel: «Зберегти як CSV UTF-8». Розміри і кольори через <code>|</code></p>
                <input type="file" name="csv_file" accept=".csv,text/csv" required class="adm-file-input">
                @error('csv_file')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="adm-import-card">
                <span class="adm-import-step">2</span>
                <h3>ZIP з фото</h3>
                <p>До ~500 МБ через браузер. Імена = колонки <code>main_image</code>, <code>gallery_images</code></p>
                <input type="file" name="images_zip" accept=".zip,application/zip" class="adm-file-input">
                @error('images_zip')<small class="form-error">{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="adm-import-help">
            <h4>Колонки CSV</h4>
            <code>name, category_slug, price, old_price, stock, description, sizes, colors, main_image, gallery_images, is_featured</code>
            <p><code>is_featured</code> — <code>1</code> або <code>0</code> (показати на головній). <code>old_price</code> — для знижки, можна порожньо.</p>
            <p>Кольори (точна назва): Чорний, Білий, Сірий, Бежевий, Синій, Червоний, Хакі…</p>
            <p>Категорії в системі:</p>
            <div class="adm-import-tags">
                @foreach ($categories as $category)
                    <span>{{ $category->name }} → <strong>{{ $category->slug }}</strong></span>
                @endforeach
            </div>
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn btn-dark">Запустити імпорт</button>
            <a href="{{ url('/admin/products') }}" class="btn btn-light">Назад до товарів</a>
        </div>
    </form>
</section>
@endsection
