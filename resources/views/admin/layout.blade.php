<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Адмін панель') | Admin Panel</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <div class="admin-logo">CS</div>
            <div>
                <strong>ClothStore</strong>
                <p>Administrator</p>
            </div>
        </div>

        <nav class="admin-nav">
            <a href="{{ url('/admin') }}" class="admin-nav-link {{ request()->is('admin') ? 'active' : '' }}">Панель управління</a>
            <a href="{{ url('/admin/orders') }}" class="admin-nav-link {{ request()->is('admin/orders*') ? 'active' : '' }}">Замовлення</a>
            <a href="{{ url('/admin/products') }}" class="admin-nav-link {{ request()->is('admin/products') ? 'active' : '' }}">Товари</a>
            <a href="{{ url('/admin/products/create') }}" class="admin-nav-link {{ request()->is('admin/products/create') ? 'active' : '' }}">Додати товар</a>
            <a href="{{ url('/admin/categories') }}" class="admin-nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}">Категорії</a>
            <a href="{{ url('/admin/users') }}" class="admin-nav-link {{ request()->is('admin/users') ? 'active' : '' }}">Користувачі</a>
            <a href="{{ url('/admin/users/create') }}" class="admin-nav-link {{ request()->is('admin/users/create') ? 'active' : '' }}">Створити користувача</a>
            <a href="{{ url('/') }}" class="admin-nav-link">На сайт</a>
            <form action="{{ url('/logout') }}" method="POST" class="mobile-logout-form">
                @csrf
                <button type="submit" class="admin-nav-link mobile-logout-btn">Вийти</button>
            </form>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-topbar">
            <div>
                <h1>@yield('title', 'Адмін панель')</h1>
                <p>Керування магазином, товарами та користувачами</p>
            </div>
        </div>

        @yield('admin_content')
    </main>
</div>

<script src="{{ asset('assets/js/app.js') }}"></script>
@yield('admin_scripts')
</body>
</html>
