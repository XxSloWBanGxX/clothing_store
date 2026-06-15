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

<footer class="site-footer site-footer-v2">
    <div class="footer-top-strip">
        <div class="container footer-top-strip-inner">
            <span>Нова колекція вже в каталозі</span>
            <a href="{{ url('/new') }}" class="footer-top-link">Дивитись новинки →</a>
        </div>
    </div>

    <div class="container footer-v2-grid">
        <div class="footer-v2-brand">
            <a href="{{ url('/') }}" class="footer-logo">CLOTH<span>STORE</span></a>
            <p>Сучасний одяг у мінімалістичному стилі. Зручний онлайн-магазин з доставкою по всій Україні.</p>
            <a href="https://www.instagram.com/tori_cloth.store?utm_source=qr" target="_blank" rel="noopener" class="footer-social-v2" aria-label="Instagram">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                @tori_cloth.store
            </a>
        </div>

        <div class="footer-v2-col">
            <h4>Магазин</h4>
            <nav class="footer-v2-links">
                <a href="{{ url('/') }}">Головна</a>
                <a href="{{ url('/catalog') }}">Каталог</a>
                <a href="{{ url('/new') }}">Новинки</a>
                <a href="{{ url('/favorites') }}">Обране</a>
                <a href="{{ url('/cart') }}">Кошик</a>
            </nav>
        </div>

        <div class="footer-v2-col">
            <h4>Інформація</h4>
            <nav class="footer-v2-links">
                <a href="{{ url('/about') }}">Про нас</a>
                <a href="{{ url('/cooperation') }}">Співробітництво</a>
                <a href="{{ url('/privacy') }}">Політика конфіденційності</a>
                @guest
                    <a href="{{ url('/login') }}">Увійти</a>
                    <a href="{{ url('/register') }}">Реєстрація</a>
                @endguest
            </nav>
        </div>

        <div class="footer-v2-col">
            <h4>Контакти</h4>
            <ul class="footer-v2-contacts">
                <li>
                    <span class="footer-contact-label">Email</span>
                    <a href="mailto:info@clothstore.local">info@clothstore.local</a>
                </li>
                <li>
                    <span class="footer-contact-label">Телефон</span>
                    <a href="tel:+380990000000">+380 99 000 00 00</a>
                </li>
                <li>
                    <span class="footer-contact-label">Локація</span>
                    <span>Україна</span>
                </li>
            </ul>
            <div class="footer-v2-badges">
                <span>Nova Poshta</span>
                <span>Ukrposhta</span>
                <span>Meest</span>
            </div>
        </div>
    </div>

    <div class="footer-v2-bottom">
        <div class="container footer-v2-bottom-inner">
            <p>© {{ date('Y') }} ClothStore. Усі права захищені.</p>
            <a href="{{ url('/catalog') }}" class="footer-v2-cta">Перейти до покупок</a>
        </div>
    </div>
</footer>

<div class="floating-tools">
    <button type="button" class="floating-btn floating-top" id="backToTop" aria-label="Нагору" title="Нагору">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    </button>

    <button type="button" class="floating-btn floating-support" id="supportBtn" aria-label="Підтримка" title="Підтримка">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
        </svg>
    </button>
</div>

<div class="support-panel {{ session('supportSuccess') ? 'open' : '' }}" id="supportPanel">
    <div class="support-panel-head">
        <div>
            <strong>Підтримка ClothStore</strong>
            <p>Зазвичай відповідаємо протягом дня</p>
        </div>
        <button type="button" class="support-panel-close" id="supportClose" aria-label="Закрити">×</button>
    </div>

    <div class="support-panel-body">
        @if (session('supportSuccess'))
            <div class="alert-success">{{ session('supportSuccess') }}</div>
        @endif

        <p class="support-intro">Привіт! 👋 Напиши своє питання — і ми звʼяжемося з тобою.</p>

        <form action="{{ url('/support') }}" method="POST" class="support-form">
            @csrf

            <div class="form-group">
                <label for="support_name">Імʼя</label>
                <input type="text" id="support_name" name="name" value="{{ auth()->check() ? auth()->user()->name : old('name') }}" required>
                @error('name')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label for="support_email">Email</label>
                <input type="email" id="support_email" name="email" value="{{ auth()->check() ? auth()->user()->email : old('email') }}" required>
                @error('email')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label for="support_message">Повідомлення</label>
                <textarea id="support_message" name="message" rows="4" placeholder="Опиши своє питання..." required>{{ old('message') }}</textarea>
                @error('message')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <button type="submit" class="btn btn-dark" style="width:100%;">Надіслати</button>
        </form>
    </div>
</div>

<script src="{{ asset('assets/js/app.js') }}"></script>

<script>
    (function () {
        const supportBtn = document.getElementById('supportBtn');
        const supportPanel = document.getElementById('supportPanel');
        const supportClose = document.getElementById('supportClose');
        const backToTop = document.getElementById('backToTop');

        if (supportBtn && supportPanel) {
            supportBtn.addEventListener('click', function () {
                supportPanel.classList.toggle('open');
            });
        }
        if (supportClose && supportPanel) {
            supportClose.addEventListener('click', function () {
                supportPanel.classList.remove('open');
            });
        }

        if (backToTop) {
            const toggleTop = function () {
                if (window.scrollY > 400) {
                    backToTop.classList.add('show');
                } else {
                    backToTop.classList.remove('show');
                }
            };
            window.addEventListener('scroll', toggleTop);
            toggleTop();

            backToTop.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    })();

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