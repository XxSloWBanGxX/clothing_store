@extends('admin.layout')

@section('title', 'Підтримка')

@section('admin_content')

@if (session('status'))
    <div class="alert-success">{{ session('status') }}</div>
@endif

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Звернення в підтримку</h2>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Імʼя</th>
                    <th>Email</th>
                    <th>Повідомлення</th>
                    <th>Статус</th>
                    <th>Дата</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($messages as $msg)
                    <tr>
                        <td>#{{ (int) $msg->id }}</td>
                        <td>{{ $msg->name }}</td>
                        <td>{{ $msg->email }}</td>
                        <td style="max-width:340px; white-space:normal;">{{ $msg->message }}</td>
                        <td>
                            @if ($msg->status === 'resolved')
                                <span class="admin-badge success">Опрацьовано</span>
                            @else
                                <span class="admin-badge danger">Нове</span>
                            @endif
                        </td>
                        <td>{{ $msg->created_at }}</td>
                        <td>
                            <div class="admin-actions">
                                @if ($msg->status !== 'resolved')
                                    <form action="{{ url('/admin/support/' . $msg->id . '/resolve') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-light btn-sm">Опрацьовано</button>
                                    </form>
                                @endif
                                <form action="{{ url('/admin/support/' . $msg->id) }}" method="POST" onsubmit="return confirm('Видалити звернення?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-dark btn-sm">Видалити</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Звернень ще немає.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@endsection
