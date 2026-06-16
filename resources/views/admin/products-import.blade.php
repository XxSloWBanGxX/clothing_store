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
            <p>Завантаж CSV з даними та ZIP з фото — система створить товари автоматично</p>
        </div>
        <a href="{{ url('/admin/products/import/template') }}" class="btn btn-light btn-sm">Завантажити шаблон CSV</a>
    </div>

    <form action="{{ url('/admin/products/import') }}" method="POST" enctype="multipart/form-data" class="adm-form adm-import-form">
        @csrf

        <div class="adm-import-grid">
            <div class="adm-import-card">
                <span class="adm-import-step">1</span>
                <h3>CSV файл</h3>
                <p>Один рядок = один товар. Розміри та кольори через <code>|</code></p>
                <input type="file" name="csv_file" accept=".csv,text/csv" required class="adm-file-input">
                @error('csv_file')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="adm-import-card">
                <span class="adm-import-step">2</span>
                <h3>ZIP з фото (необовʼязково)</h3>
                <p>Назви файлів мають збігатися з колонками <code>main_image</code> та <code>gallery_images</code></p>
                <input type="file" name="images_zip" accept=".zip,application/zip" class="adm-file-input">
                @error('images_zip')<small class="form-error">{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="adm-import-help">
            <h4>Колонки CSV</h4>
            <code>name, category_slug, price, old_price, stock, description, sizes, colors, main_image, gallery_images</code>
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
