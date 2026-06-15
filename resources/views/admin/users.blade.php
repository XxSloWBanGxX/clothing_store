@extends('admin.layout')

@section('title', 'Користувачі')

@section('admin_content')

@if (session('status'))
    <div class="alert-success">{{ session('status') }}</div>
@endif

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Користувачі</h2>
        <a href="{{ url('/admin/users/create') }}" class="btn btn-dark">+ Створити користувача</a>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Імʼя</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Роль</th>
                    <th>Статус</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>#{{ (int) $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->role }}</td>
                        <td>
                            @if (! empty($user->is_verified))
                                <span class="admin-badge success">Підтверджений</span>
                            @else
                                <span class="admin-badge danger">Не підтверджений</span>
                            @endif
                        </td>
                        <td>
                            <div class="admin-actions">
                                @if ((int) $user->id !== (int) auth()->id())
                                    <form action="{{ url('/admin/users/' . $user->id) }}" method="POST" onsubmit="return confirm('Видалити користувача?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-dark btn-sm">Видалити</button>
                                    </form>
                                @else
                                    <span class="admin-badge success">Ти</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Користувачів поки немає.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@endsection
