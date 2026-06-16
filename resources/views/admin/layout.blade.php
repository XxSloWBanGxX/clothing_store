<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Адмін панель') | ClothStore Admin</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body class="admin-body admin-v2">
@php
    $navCounts = $adminNav ?? [];
    $pendingOrders = (int) ($navCounts['orders_new'] ?? 0);
    $pendingSupport = (int) ($navCounts['support_new'] ?? 0);
@endphp

<div class="adm-shell">
    <div class="adm-overlay" id="admOverlay" hidden></div>

    <aside class="adm-sidebar" id="admSidebar">
        <div class="adm-brand">
            <a href="{{ url('/admin') }}" class="adm-brand-link">
                <span class="adm-brand-mark">CS</span>
                <span class="adm-brand-text">
                    <strong>CLOTHSTORE</strong>
                    <small>Admin Panel</small>
                </span>
            </a>
        </div>

        <nav class="adm-nav">
            <div class="adm-nav-group">
                <span class="adm-nav-label">Головне</span>
                <a href="{{ url('/admin') }}" class="adm-nav-link {{ request()->path() === 'admin' ? 'is-active' : '' }}">
                    <span class="adm-nav-icon">◫</span>
                    <span>Дашборд</span>
                </a>
            </div>

            <div class="adm-nav-group">
                <span class="adm-nav-label">Продажі</span>
                <a href="{{ url('/admin/orders') }}" class="adm-nav-link {{ request()->is('admin/orders*') ? 'is-active' : '' }}">
                    <span class="adm-nav-icon">🛒</span>
                    <span>Замовлення</span>
                    @if ($pendingOrders > 0)
                        <span class="adm-nav-badge">{{ $pendingOrders }}</span>
                    @endif
                </a>
                <a href="{{ url('/admin/reviews') }}" class="adm-nav-link {{ request()->is('admin/reviews*') ? 'is-active' : '' }}">
                    <span class="adm-nav-icon">★</span>
                    <span>Відгуки</span>
                </a>
            </div>

            <div class="adm-nav-group">
                <span class="adm-nav-label">Каталог</span>
                <a href="{{ url('/admin/products') }}" class="adm-nav-link {{ request()->is('admin/products') && ! request()->is('admin/products/create') ? 'is-active' : '' }}">
                    <span class="adm-nav-icon">▦</span>
                    <span>Товари</span>
                </a>
                <a href="{{ url('/admin/products/create') }}" class="adm-nav-link {{ request()->is('admin/products/create') ? 'is-active' : '' }}">
                    <span class="adm-nav-icon">＋</span>
                    <span>Додати товар</span>
                </a>
                <a href="{{ url('/admin/products/import') }}" class="adm-nav-link {{ request()->is('admin/products/import*') ? 'is-active' : '' }}">
                    <span class="adm-nav-icon">⇪</span>
                    <span>Імпорт товарів</span>
                </a>
                <a href="{{ url('/admin/categories') }}" class="adm-nav-link {{ request()->is('admin/categories*') ? 'is-active' : '' }}">
                    <span class="adm-nav-icon">☰</span>
                    <span>Категорії</span>
                </a>
            </div>

            <div class="adm-nav-group">
                <span class="adm-nav-label">Клієнти</span>
                <a href="{{ url('/admin/users') }}" class="adm-nav-link {{ request()->is('admin/users*') ? 'is-active' : '' }}">
                    <span class="adm-nav-icon">👤</span>
                    <span>Користувачі</span>
                </a>
                <a href="{{ url('/admin/messages') }}" class="adm-nav-link {{ request()->is('admin/messages*') ? 'is-active' : '' }}">
                    <span class="adm-nav-icon">✉</span>
                    <span>Повідомлення</span>
                    @if (($navCounts['messages_unread'] ?? 0) > 0)
                        <span class="adm-nav-badge">{{ (int) $navCounts['messages_unread'] }}</span>
                    @endif
                </a>
                <a href="{{ url('/admin/support') }}" class="adm-nav-link {{ request()->is('admin/support*') ? 'is-active' : '' }}">
                    <span class="adm-nav-icon">💬</span>
                    <span>Підтримка</span>
                    @if ($pendingSupport > 0)
                        <span class="adm-nav-badge">{{ $pendingSupport }}</span>
                    @endif
                </a>
            </div>
        </nav>

        <div class="adm-sidebar-foot">
            <a href="{{ url('/') }}" class="adm-foot-link">← На сайт</a>
            <form action="{{ url('/logout') }}" method="POST">
                @csrf
                <button type="submit" class="adm-foot-link adm-foot-btn">Вийти</button>
            </form>
        </div>
    </aside>

    <div class="adm-main">
        <header class="adm-topbar">
            <button type="button" class="adm-menu-toggle" id="admMenuToggle" aria-label="Меню">
                <span></span><span></span><span></span>
            </button>
            <div class="adm-topbar-copy">
                <span class="section-label">ADMIN</span>
                <h1>@yield('title', 'Адмін панель')</h1>
            </div>
            <div class="adm-topbar-user">
                <span class="adm-user-name">{{ auth()->user()->name ?? 'Admin' }}</span>
                <span class="adm-user-role">Адміністратор</span>
            </div>
        </header>

        <div class="adm-page">
            @yield('admin_content')
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="{{ asset('assets/js/admin.js') }}"></script>
@yield('admin_scripts')
</body>
</html>
