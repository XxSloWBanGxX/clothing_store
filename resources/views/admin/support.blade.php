@extends('admin.layout')

@section('title', 'Підтримка')

@section('admin_content')
@include('partials.admin-flash')

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Звернення в підтримку</h2>
            <p>{{ $messages->count() }} повідомлень</p>
        </div>
    </div>

    <form action="{{ url('/admin/support') }}" method="GET" class="adm-toolbar">
        <select name="status" class="adm-select">
            <option value="">Усі звернення</option>
            <option value="new" {{ ($statusFilter ?? '') === 'new' ? 'selected' : '' }}>Нові</option>
            <option value="resolved" {{ ($statusFilter ?? '') === 'resolved' ? 'selected' : '' }}>Опрацьовані</option>
        </select>
        <button type="submit" class="btn btn-dark btn-sm">Фільтрувати</button>
        @if (! empty($statusFilter))
            <a href="{{ url('/admin/support') }}" class="btn btn-light btn-sm">Скинути</a>
        @endif
    </form>

    <div class="adm-support-list">
        @forelse ($messages as $msg)
            <article class="adm-support-card {{ $msg->status === 'resolved' ? 'is-resolved' : '' }}">
                <div class="adm-support-head">
                    <div>
                        <strong>{{ $msg->name }}</strong>
                        <span class="adm-cell-muted">{{ $msg->email }}</span>
                    </div>
                    <div class="adm-support-meta">
                        @if ($msg->status === 'resolved')
                            <span class="adm-badge adm-badge--success">Опрацьовано</span>
                        @else
                            <span class="adm-badge adm-badge--warning">Нове</span>
                        @endif
                        <span class="adm-cell-muted">{{ $msg->created_at }}</span>
                    </div>
                </div>
                <p class="adm-support-text">{{ $msg->message }}</p>
                <div class="adm-row-actions">
                    @if ($msg->status !== 'resolved')
                        <form action="{{ url('/admin/support/' . $msg->id . '/resolve') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-dark btn-sm">Позначити опрацьованим</button>
                        </form>
                    @endif
                    <form action="{{ url('/admin/support/' . $msg->id) }}" method="POST" onsubmit="return confirm('Видалити звернення?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-light btn-sm">Видалити</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="adm-empty">
                <h3>Звернень немає</h3>
                <p>Нові повідомлення з форми підтримки зʼявляться тут.</p>
            </div>
        @endforelse
    </div>
</section>
@endsection
