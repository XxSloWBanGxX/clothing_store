@extends('admin.layout')

@section('title', 'Редагування категорії')

@section('admin_content')
@include('partials.admin-flash')

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>{{ $category->name }}</h2>
            <p>ID #{{ $category->id }}</p>
        </div>
        <a href="{{ url('/admin/categories') }}" class="btn btn-light btn-sm">← Назад</a>
    </div>

    <form action="{{ url('/admin/categories/' . $category->id) }}" method="POST" class="adm-form">
        @csrf
        @method('PUT')
        <div class="admin-form-grid">
            <div class="form-group">
                <label for="name">Назва</label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}">
                @error('name')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $category->slug) }}">
                @error('slug')<small class="form-error">{{ $message }}</small>@enderror
            </div>
        </div>
        <div class="admin-form-actions">
            <button type="submit" class="btn btn-dark">Зберегти</button>
        </div>
    </form>
</section>
@endsection
