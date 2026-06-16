@extends('admin.layout')

@section('title', 'Категорії')

@section('admin_content')
@include('partials.admin-flash')

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Додати категорію</h2>
            <p>Нова категорія зʼявиться в каталозі</p>
        </div>
    </div>

    <form action="{{ url('/admin/categories') }}" method="POST" class="adm-form">
        @csrf
        <div class="admin-form-grid">
            <div class="form-group">
                <label for="name">Назва</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Напр.: Аксесуари">
                @error('name')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="slug">Slug (необовʼязково)</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug') }}" placeholder="accessories">
                @error('slug')<small class="form-error">{{ $message }}</small>@enderror
            </div>
        </div>
        <div class="admin-form-actions">
            <button type="submit" class="btn btn-dark">Створити категорію</button>
        </div>
    </form>
</section>

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Усі категорії</h2>
            <p>{{ $categories->count() }} категорій</p>
        </div>
    </div>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>Slug</th>
                    <th>Товарів</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>#{{ (int) $category->id }}</td>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td>{{ $category->slug }}</td>
                        <td><span class="adm-badge adm-badge--neutral">{{ (int) $category->products_count }}</span></td>
                        <td>
                            <div class="adm-row-actions">
                                <a href="{{ url('/catalog?category=' . $category->slug) }}" class="btn btn-light btn-sm" target="_blank">На сайті</a>
                                <form action="{{ url('/admin/categories/' . $category->id) }}" method="POST" onsubmit="return confirm('Видалити категорію?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm">Видалити</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Категорій ще немає.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
