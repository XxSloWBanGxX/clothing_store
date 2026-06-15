@extends('admin.layout')

@section('title', 'Адмін панель')

@section('admin_content')

@if (session('status'))
    <div class="alert-success">{{ session('status') }}</div>
@endif

<section class="admin-cards">
    <div class="admin-stat-card">
        <span>Всього товарів</span>
        <strong>{{ (int) $stats['products'] }}</strong>
    </div>

    <div class="admin-stat-card">
        <span>В наявності</span>
        <strong>{{ (int) $stats['inStock'] }}</strong>
    </div>

    <div class="admin-stat-card">
        <span>Популярні товари</span>
        <strong>{{ (int) $stats['featured'] }}</strong>
    </div>

    <div class="admin-stat-card">
        <span>Категорії</span>
        <strong>{{ (int) $stats['categories'] }}</strong>
    </div>

    <div class="admin-stat-card">
        <span>Користувачі</span>
        <strong>{{ (int) $stats['users'] }}</strong>
    </div>

    <div class="admin-stat-card">
        <span>Підтримка</span>
        <strong>{{ (int) $stats['support'] }}</strong>
    </div>
</section>

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Швидкі дії</h2>
    </div>

    <div class="admin-quick-actions">
        <a href="{{ url('/admin/products') }}" class="btn btn-dark">Перейти до товарів</a>
        <a href="{{ url('/admin/products/create') }}" class="btn btn-light">Додати новий товар</a>
        <a href="{{ url('/admin/users') }}" class="btn btn-light">Переглянути користувачів</a>
        <a href="{{ url('/admin/users/create') }}" class="btn btn-light">Створити користувача</a>
    </div>
</section>

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Огляд панелі</h2>
    </div>
    <p class="admin-text">
        У цій версії адмінки можна керувати товарами, кількістю, галереєю з кількох фото, а також переглядати і створювати користувачів та керувати замовленнями.
    </p>
</section>

@endsection
