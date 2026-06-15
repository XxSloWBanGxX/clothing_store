@extends('admin.layout')

@section('title', 'Створити користувача')

@section('admin_content')

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Створити користувача</h2>
    </div>

    <form action="{{ url('/admin/users') }}" method="POST" class="admin-form">
        @csrf

        <div class="admin-form-grid">
            <div class="form-group">
                <label for="name">Імʼя</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}">
                @error('name')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}">
                @error('username')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}">
                @error('email')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label for="phone">Телефон</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}">
                @error('phone')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password">
                @error('password')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label for="role">Роль</label>
                <select id="role" name="role">
                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>user</option>
                    <option value="support" {{ old('role') === 'support' ? 'selected' : '' }}>support</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>admin</option>
                </select>
                @error('role')<small class="form-error">{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="filter-checkbox">
            <label>
                <input type="checkbox" name="is_verified" value="1" {{ old('is_verified') ? 'checked' : '' }}>
                Одразу позначити як підтвердженого
            </label>
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn btn-dark">Створити користувача</button>
            <a href="{{ url('/admin/users') }}" class="btn btn-light">Назад</a>
        </div>
    </form>
</section>

@endsection
