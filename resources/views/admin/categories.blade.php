@extends('admin.layout')

@section('title', 'Категорії')

@section('admin_content')

@if (session('status'))
    <div class="alert-success">{{ session('status') }}</div>
@endif

@if ($errors->has('delete'))
    <div class="alert-error">{{ $errors->first('delete') }}</div>
@endif

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Додати категорію</h2>
    </div>

    <form action="{{ url('/admin/categories') }}" method="POST" class="admin-form">
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

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Усі категорії</h2>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
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
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->slug }}</td>
                        <td>{{ (int) $category->products_count }}</td>
                        <td>
                            <div class="admin-actions">
                                <a href="{{ url('/catalog?category=' . $category->slug) }}" class="btn btn-light btn-sm" target="_blank">Переглянути</a>
                                <form action="{{ url('/admin/categories/' . $category->id) }}" method="POST" onsubmit="return confirm('Видалити категорію?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-dark btn-sm">Видалити</button>
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
