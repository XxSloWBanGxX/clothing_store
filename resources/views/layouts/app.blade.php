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

            <div class="nav-dropdown">
                <a href="{{ url('/catalog') }}" class="nav-link">Каталог ▾</a>
                @if (! empty($navCategories) && count($navCategories) > 0)
                    <div class="nav-dropdown-menu">
                        <a href="{{ url('/catalog') }}" class="nav-dropdown-link">Усі товари</a>
                        @foreach ($navCategories as $cat)
                            <a href="{{ url('/catalog?category=' . $cat->slug) }}" class="nav-dropdown-link">{{ $cat->name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            <a href="{{ url('/new') }}" class="nav-link">Новинки</a>
            <a href="{{ url('/about') }}" class="nav-link">Про нас</a>
        </nav>

        <div class="header-actions">
            <form action="{{ url('/catalog') }}" method="GET" class="header-search">
                <span class="header-search-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input type="text" name="search" placeholder="Пошук товарів..." value="{{ request('search') }}">
                <button type="submit">Знайти</button>
            </form>

            <a href="{{ url('/cart') }}" class="cart-widget" aria-label="Кошик">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                @if (($cartCount ?? 0) > 0)
                    <span class="cart-widget-badge">{{ $cartCount }}</span>
                @endif
            </a>

            @auth
                @php $authUser = auth()->user(); @endphp
                <div class="user-menu">
                    <button type="button" class="user-menu-trigger" id="userMenuBtn">
                        <span class="user-avatar">
                            @if (! empty($authUser->avatar))
                                <img src="{{ asset('assets/images/avatars/' . $authUser->avatar) }}" alt="{{ $authUser->username }}">
                            @else
                                <span class="user-avatar-initial">{{ mb_strtoupper(mb_substr($authUser->username ?? $authUser->name ?? 'U', 0, 1)) }}</span>
                            @endif
                        </span>
                        <span class="user-meta">
                            <strong>{{ $authUser->username ?? '—' }}</strong>
                            <small>({{ $authUser->name ?? '' }})</small>
                        </span>
                        <span class="user-chevron">▾</span>
                    </button>

                    <div class="user-dropdown" id="userDropdown">
                        <a href="{{ url('/profile') }}" class="user-dropdown-link">Мій профіль</a>
                        <a href="{{ url('/favorites') }}" class="user-dropdown-link">Обране</a>
                        <a href="{{ url('/cart') }}" class="user-dropdown-link">Кошик</a>
                        @if($authUser->role === 'admin')
                            <a href="{{ url('/admin') }}" class="user-dropdown-link">Адмін-панель</a>
                        @endif
                        <form action="{{ url('/logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="user-dropdown-link user-dropdown-logout">Вийти</button>
                        </form>
                    </div>
                </div>
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
        @if (! empty($navCategories))
            @foreach ($navCategories as $cat)
                <a href="{{ url('/catalog?category=' . $cat->slug) }}" class="mobile-link mobile-sublink">— {{ $cat->name }}</a>
            @endforeach
        @endif
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

<script>
    (function () {
        const btn = document.getElementById('userMenuBtn');
        const dropdown = document.getElementById('userDropdown');
        if (!btn || !dropdown) return;

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('open');
        });

        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });
    })();
</script>

</body>
</html>