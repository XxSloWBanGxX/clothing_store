@extends('admin.layout')

@section('title', 'Користувачі')

@section('admin_content')
@include('partials.admin-flash')

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Користувачі</h2>
            <p>{{ $users->count() }} акаунтів</p>
        </div>
        <a href="{{ url('/admin/users/create') }}" class="btn btn-dark">+ Створити</a>
    </div>

    <form action="{{ url('/admin/users') }}" method="GET" class="adm-toolbar">
        <div class="adm-search">
            <input type="search" name="q" value="{{ $search ?? '' }}" placeholder="Імʼя, username, email, телефон…">
        </div>
        <select name="role" class="adm-select">
            <option value="">Усі ролі</option>
            @foreach (['user' => 'Користувач', 'admin' => 'Адмін', 'support' => 'Підтримка'] as $value => $label)
                <option value="{{ $value }}" {{ ($roleFilter ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-dark btn-sm">Фільтрувати</button>
        @if (! empty($search) || ! empty($roleFilter))
            <a href="{{ url('/admin/users') }}" class="btn btn-light btn-sm">Скинути</a>
        @endif
    </form>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Користувач</th>
                    <th>Контакти</th>
                    <th>Роль</th>
                    <th>Статус</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>#{{ (int) $user->id }}</td>
                        <td>
                            <a href="{{ url('/admin/users/' . $user->id) }}" class="adm-user-link">
                                <strong>{{ $user->name }}</strong>
                                <span class="adm-cell-muted">{{ '@' . $user->username }}</span>
                            </a>
                        </td>
                        <td>
                            <span>{{ $user->email }}</span>
                            <span class="adm-cell-muted">{{ $user->phone }}</span>
                        </td>
                        <td>
                            @php
                                $roleTone = match ($user->role) {
                                    'admin' => 'dark',
                                    'support' => 'info',
                                    default => 'neutral',
                                };
                            @endphp
                            <span class="adm-badge adm-badge--{{ $roleTone }}">{{ $user->role }}</span>
                        </td>
                        <td>
                            @if (! empty($user->is_verified))
                                <span class="adm-badge adm-badge--success">Підтверджений</span>
                            @else
                                <span class="adm-badge adm-badge--warning">Не підтверджений</span>
                            @endif
                        </td>
                        <td>
                            <div class="adm-row-actions">
                                <a href="{{ url('/admin/users/' . $user->id) }}" class="btn btn-dark btn-sm">Профіль</a>
                                @if ((int) $user->id !== (int) auth()->id())
                                    <form action="{{ url('/admin/users/' . $user->id) }}" method="POST" onsubmit="return confirm('Видалити користувача?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-sm">Видалити</button>
                                    </form>
                                @else
                                    <span class="adm-badge adm-badge--info">Це ти</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Користувачів не знайдено.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
