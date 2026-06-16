@extends('admin.layout')

@section('title', 'Повідомлення')

@section('admin_content')
@include('partials.admin-flash')

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Діалоги з клієнтами</h2>
            <p>Прямий звʼязок з користувачами магазину</p>
        </div>
    </div>

    <form action="{{ url('/admin/messages') }}" method="GET" class="adm-toolbar">
        <select name="status" class="adm-select">
            <option value="">Усі діалоги</option>
            <option value="open" {{ ($statusFilter ?? '') === 'open' ? 'selected' : '' }}>Відкриті</option>
            <option value="closed" {{ ($statusFilter ?? '') === 'closed' ? 'selected' : '' }}>Закриті</option>
        </select>
        <button type="submit" class="btn btn-dark btn-sm">Фільтрувати</button>
    </form>

    <form action="{{ url('/admin/messages/start') }}" method="POST" class="adm-new-chat-form">
        @csrf
        <select name="user_id" class="adm-select" required>
            <option value="">Обрати користувача…</option>
            @foreach ($users ?? [] as $user)
                <option value="{{ $user->id }}">{{ $user->name }} · {{ $user->email }}</option>
            @endforeach
        </select>
        <textarea name="body" rows="3" placeholder="Перше повідомлення клієнту…" required>{{ old('body') }}</textarea>
        <button type="submit" class="btn btn-dark btn-sm">Розпочати діалог</button>
    </form>

    <div class="adm-chat-list">
        @forelse ($conversations as $conversation)
            @php
                $title = $conversation->user_name ?: $conversation->guest_name;
                $email = $conversation->user_email ?: $conversation->guest_email;
            @endphp
            <a href="{{ url('/admin/messages/' . $conversation->id) }}" class="adm-chat-list-item {{ (int) $conversation->unread_count > 0 ? 'has-unread' : '' }}">
                <div class="adm-chat-list-main">
                    <strong>{{ $title }}</strong>
                    <span class="adm-cell-muted">{{ $email }}</span>
                    <p>{{ \Illuminate\Support\Str::limit($conversation->last_preview, 90) }}</p>
                </div>
                <div class="adm-chat-list-meta">
                    @if ((int) $conversation->unread_count > 0)
                        <span class="adm-nav-badge">{{ (int) $conversation->unread_count }}</span>
                    @endif
                    <span class="adm-badge adm-badge--{{ $conversation->status === 'open' ? 'success' : 'neutral' }}">
                        {{ $conversation->status === 'open' ? 'Відкрито' : 'Закрито' }}
                    </span>
                    <small>{{ $conversation->last_message_at ?? $conversation->created_at }}</small>
                </div>
            </a>
        @empty
            <div class="adm-empty">
                <h3>Діалогів поки немає</h3>
                <p>Коли користувач напише в підтримку або ти розпочнеш чат — він зʼявиться тут.</p>
            </div>
        @endforelse
    </div>
</section>
@endsection
