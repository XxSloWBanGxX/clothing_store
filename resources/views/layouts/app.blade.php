<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Магазин одягу')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>

<header class="site-header">
    <div class="container header-container">
        <a href="{{ url('/') }}" class="logo">CLOTH<span>STORE</span></a>

        <nav class="main-nav">
            <a href="{{ url('/') }}" class="nav-link">Головна</a>
            <a href="{{ url('/catalog') }}" class="nav-link">Каталог</a>
            <a href="{{ url('/new') }}" class="nav-link">Новинки</a>
            <a href="{{ url('/about') }}" class="nav-link">Про нас</a>
        </nav>

        <div class="header-actions">
            <a href="{{ url('/catalog') }}" class="action-link">Пошук</a>

            @auth
                <a href="{{ url('/profile') }}" class="action-link">Профіль</a>
            @endauth

            <a href="{{ url('/favorites') }}" class="action-link">
                Обране <span class="cart-count">0</span>
            </a>

            <a href="{{ url('/cart') }}" class="action-link cart-link">
                Кошик <span class="cart-count">0</span>
            </a>

            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ url('/admin') }}" class="action-link">Адмін</a>
                @endif
                <form action="{{ url('/logout') }}" method="POST" class="logout-form">
                    @csrf
                    <button type="submit" class="btn btn-outline">Вийти</button>
                </form>
            @else
                <a href="{{ url('/login') }}" class="btn btn-outline">Увійти</a>
            @endauth
        </div>

        <button class="burger" id="burgerBtn" aria-label="Відкрити меню">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <div class="mobile-menu" id="mobileMenu">
        <a href="{{ url('/') }}" class="mobile-link">Головна</a>
        <a href="{{ url('/catalog') }}" class="mobile-link">Каталог</a>
        <a href="{{ url('/new') }}" class="mobile-link">Новинки</a>
        <a href="{{ url('/about') }}" class="mobile-link">Про нас</a>
        <a href="{{ url('/favorites') }}" class="mobile-link">Обране</a>
        <a href="{{ url('/cart') }}" class="mobile-link">Кошик</a>
        @auth
            <a href="{{ url('/profile') }}" class="mobile-link">Профіль</a>
            @if(auth()->user()->role === 'admin')
                <a href="{{ url('/admin') }}" class="mobile-link">Адмін</a>
            @endif
            <form action="{{ url('/logout') }}" method="POST" class="mobile-logout-form">
                @csrf
                <button type="submit" class="mobile-link mobile-logout-btn">Вийти</button>
            </form>
        @else
            <a href="{{ url('/login') }}" class="mobile-link">Увійти</a>
            <a href="{{ url('/register') }}" class="mobile-link">Реєстрація</a>
        @endauth
    </div>
</header>

@yield('content')

<footer class="site-footer">
    <div class="container footer-container">
        <div class="footer-brand">
            <h3>CLOTHSTORE</h3>
            <p>Сучасний магазин стильного одягу з мінімалістичним дизайном, зручним інтерфейсом і основою для подальшого розвитку.</p>
        </div>

        <div class="footer-column">
            <h4>Навігація</h4>
            <a href="{{ url('/') }}">Головна</a>
            <a href="{{ url('/catalog') }}">Каталог</a>
            <a href="{{ url('/new') }}">Новинки</a>
            <a href="{{ url('/about') }}">Про нас</a>
        </div>

        <div class="footer-column">
            <h4>Користувач</h4>
            <a href="{{ url('/profile') }}">Профіль</a>
            <a href="{{ url('/favorites') }}">Обране</a>
            <a href="{{ url('/cart') }}">Кошик</a>
            @guest
                <a href="{{ url('/login') }}">Увійти</a>
                <a href="{{ url('/register') }}">Реєстрація</a>
            @endguest
        </div>

        <div class="footer-column">
            <h4>Контакти</h4>
            <p>Email: info@clothstore.local</p>
            <p>Телефон: +380 99 000 00 00</p>
            <p>Україна</p>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <p>© {{ date('Y') }} ClothStore. Усі права захищені.</p>
        </div>
    </div>
</footer>

<script src="{{ asset('assets/js/app.js') }}"></script>

</body>
</html>