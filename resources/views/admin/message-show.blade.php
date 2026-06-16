@extends('admin.layout')

@section('title', 'Діалог')

@section('admin_content')
@include('partials.admin-flash')

@php
    $clientName = $conversation->user_name ?: $conversation->guest_name;
    $clientEmail = $conversation->user_email ?: $conversation->guest_email;
@endphp

<section class="adm-chat-page">
    <div class="adm-chat-page-head">
        <div>
            <a href="{{ url('/admin/messages') }}" class="adm-back-link">← Усі діалоги</a>
            <h2>{{ $clientName }}</h2>
            <p class="adm-cell-muted">{{ $clientEmail }} @if ($conversation->user_phone) · {{ $conversation->user_phone }} @endif</p>
        </div>
        <div class="adm-row-actions">
            @if ($conversation->user_id)
                <a href="{{ url('/admin/users') }}" class="btn btn-light btn-sm">Користувачі</a>
            @endif
            @if ($conversation->status === 'open')
                <form action="{{ url('/admin/messages/' . $conversation->id . '/close') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm">Закрити діалог</button>
                </form>
            @endif
        </div>
    </div>

    <div class="adm-chat-thread">
        @foreach ($messages as $message)
            <article class="adm-chat-bubble {{ $message->sender_role === 'admin' ? 'is-admin' : 'is-user' }}">
                <div class="adm-chat-bubble-meta">
                    <strong>{{ $message->sender_role === 'admin' ? 'Адміністратор' : $clientName }}</strong>
                    <span>{{ $message->created_at }}</span>
                </div>
                <p>{{ $message->body }}</p>
            </article>
        @endforeach
    </div>

    @if ($conversation->status === 'open')
        <form action="{{ url('/admin/messages/' . $conversation->id . '/reply') }}" method="POST" class="adm-chat-reply">
            @csrf
            <textarea name="body" rows="4" placeholder="Напиши відповідь клієнту..." required>{{ old('body') }}</textarea>
            @error('body')<small class="form-error">{{ $message }}</small>@enderror
            <button type="submit" class="btn btn-dark">Надіслати відповідь</button>
        </form>
    @else
        <div class="adm-empty">
            <p>Діалог закрито. Клієнт може написати знову через підтримку на сайті.</p>
        </div>
    @endif
</section>
@endsection
