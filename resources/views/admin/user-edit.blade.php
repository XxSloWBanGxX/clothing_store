@extends('admin.layout')

@section('title', 'Редагування користувача')

@section('admin_content')
@include('partials.admin-flash')

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>{{ $user->name }}</h2>
            <p>@{{ $user->username }} · #{{ $user->id }}</p>
        </div>
        <a href="{{ url('/admin/users/' . $user->id) }}" class="btn btn-light btn-sm">← Профіль</a>
    </div>

    <form action="{{ url('/admin/users/' . $user->id) }}" method="POST" class="adm-form">
        @csrf
        @method('PUT')
        <div class="admin-form-grid">
            <div class="form-group">
                <label for="name">Імʼя</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
                @error('name')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}">
                @error('username')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
                @error('email')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="phone">Телефон</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                @error('phone')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="role">Роль</label>
                <select id="role" name="role">
                    @foreach (['user' => 'Користувач', 'admin' => 'Адмін', 'support' => 'Підтримка'] as $val => $label)
                        <option value="{{ $val }}" {{ old('role', $user->role) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="bonus_points">Бонусні бали</label>
                <input type="number" min="0" id="bonus_points" name="bonus_points" value="{{ old('bonus_points', $user->bonus_points ?? 0) }}">
            </div>
            <div class="form-group">
                <label for="password">Новий пароль (необовʼязково)</label>
                <input type="password" id="password" name="password" autocomplete="new-password">
                @error('password')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group adm-checkbox-row">
                <label class="adm-check">
                    <input type="checkbox" name="is_verified" value="1" {{ old('is_verified', $user->is_verified) ? 'checked' : '' }}>
                    <span>Email підтверджено</span>
                </label>
            </div>
        </div>
        <div class="admin-form-actions">
            <button type="submit" class="btn btn-dark">Зберегти зміни</button>
        </div>
    </form>
</section>
@endsection
