@extends('admin.layout')

@section('title', 'Редагування: ' . $page->title)

@section('admin_content')
@include('partials.admin-flash')

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>{{ $page->title }}</h2>
            <p>Slug: <code>{{ $page->slug }}</code></p>
        </div>
        <a href="{{ url('/admin/pages') }}" class="btn btn-light btn-sm">← До списку</a>
    </div>

    <form action="{{ url('/admin/pages/' . $page->slug) }}" method="POST" class="adm-form">
        @csrf
        @method('PUT')

        <div class="admin-form-grid">
            <div class="form-group adm-grid-full">
                <label for="title">Заголовок</label>
                <input type="text" id="title" name="title" value="{{ old('title', $page->title) }}">
                @error('title')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group adm-grid-full">
                <label for="subtitle">Підзаголовок (hero)</label>
                <textarea id="subtitle" name="subtitle" rows="2">{{ old('subtitle', $page->subtitle) }}</textarea>
            </div>
            <div class="form-group adm-grid-full">
                <label for="content">Контент</label>
                <textarea id="content" name="content" rows="16" class="adm-textarea-lg">{{ old('content', $page->content) }}</textarea>
                <small class="adm-hint">Абзаци розділяй порожнім рядком — на сайті вони відобразяться окремими блоками.@if ($page->slug === 'about') Для блоку «Наш підхід» додай розділювач <code>---VALUES---</code>, потім: заголовок, текст, теги через кому.@endif</small>
            </div>
            <div class="form-group adm-checkbox-row">
                <label class="adm-check">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $page->is_published) ? 'checked' : '' }}>
                    <span>Опубліковано на сайті</span>
                </label>
            </div>
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn btn-dark">Зберегти сторінку</button>
            <a href="{{ url('/' . $page->slug) }}" class="btn btn-light" target="_blank">Переглянути</a>
        </div>
    </form>
</section>
@endsection
