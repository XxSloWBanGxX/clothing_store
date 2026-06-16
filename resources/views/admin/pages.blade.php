@extends('admin.layout')

@section('title', 'Сторінки сайту')

@section('admin_content')
@include('partials.admin-flash')

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Контентні сторінки</h2>
            <p>Про нас, політика конфіденційності, співробітництво</p>
        </div>
    </div>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Slug</th>
                    <th>Заголовок</th>
                    <th>Статус</th>
                    <th>Оновлено</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pages as $page)
                    <tr>
                        <td><code>{{ $page->slug }}</code></td>
                        <td><strong>{{ $page->title }}</strong></td>
                        <td>
                            @if ($page->is_published)
                                <span class="adm-badge adm-badge--success">Опубліковано</span>
                            @else
                                <span class="adm-badge adm-badge--neutral">Приховано</span>
                            @endif
                        </td>
                        <td class="adm-cell-muted">{{ $page->updated_at ?? '—' }}</td>
                        <td>
                            <div class="adm-row-actions">
                                <a href="{{ url('/' . $page->slug) }}" class="btn btn-light btn-sm" target="_blank">На сайті</a>
                                <a href="{{ url('/admin/pages/' . $page->slug . '/edit') }}" class="btn btn-dark btn-sm">Редагувати</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">Сторінок ще немає. Запусти міграції.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
