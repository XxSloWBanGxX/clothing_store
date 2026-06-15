@extends('layouts.app')

@section('title', 'Головна - CLOTHSTORE')

@section('content')

<main>
    <section class="hero-section">
        <div class="container hero-grid">
            <div class="hero-content">
                <span class="hero-badge">NEW COLLECTION</span>
                <h1>Стиль, який<br>говорить за тебе</h1>
                <p>
                    Сучасний одяг для тих, хто цінує мінімалізм, комфорт та
                    впевнений вигляд кожного дня.
                </p>

                <div class="hero-buttons">
                    <a href="{{ url('/catalog') }}" class="btn btn-dark">Перейти в каталог</a>
                    <a href="{{ url('/catalog') }}" class="btn btn-light">Дивитися новинки</a>
                </div>

                <div class="hero-stats">
                    <div class="stat-item">
                        <h3>500+</h3>
                        <p>Товарів</p>
                    </div>
                    <div class="stat-item">
                        <h3>24/7</h3>
                        <p>Онлайн замовлення</p>
                    </div>
                    <div class="stat-item">
                        <h3>100%</h3>
                        <p>Сучасний стиль</p>
                    </div>
                </div>
            </div>

            <div class="hero-visual">
                <div class="hero-card-main">
                    <div class="hero-image-placeholder">FASHION</div>
                </div>
                <div class="hero-floating-card top-card">Minimal</div>
                <div class="hero-floating-card bottom-card">Streetwear</div>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="container features-grid">
            <div class="feature-box">
                <h3>Швидке оформлення</h3>
                <p>Простий і зручний процес покупки без зайвих кроків.</p>
            </div>
            <div class="feature-box">
                <h3>Актуальні колекції</h3>
                <p>Стильні моделі одягу в сучасному мінімалістичному стилі.</p>
            </div>
            <div class="feature-box">
                <h3>Зручний інтерфейс</h3>
                <p>Приємна навігація, сучасний дизайн і база для розвитку магазину.</p>
            </div>
        </div>
    </section>

    @if (! empty($data['categories']) && count($data['categories']) > 0)
        <section class="categories-section">
            <div class="container">
                <div class="section-head">
                    <div>
                        <span class="section-label">CATEGORIES</span>
                        <h2>Категорії</h2>
                    </div>
                    <a href="{{ url('/catalog') }}" class="section-link">Усі товари</a>
                </div>

                <div class="categories-grid">
                    @foreach ($data['categories'] as $category)
                        <a href="{{ url('/catalog?category=' . $category->slug) }}" class="category-card">
                            <div class="category-card-image">
                                @if (! empty($category->sample_image))
                                    <img
                                        src="{{ asset('assets/images/products/' . $category->sample_image) }}"
                                        alt="{{ $category->name }}"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                    >
                                    <div class="image-placeholder" style="display:none;">{{ $category->name }}</div>
                                @else
                                    <div class="image-placeholder">{{ $category->name }}</div>
                                @endif
                            </div>
                            <div class="category-card-info">
                                <h3>{{ $category->name }}</h3>
                                <span>{{ (int) $category->products_count }} товарів</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="products-section">
        <div class="container">
            <div class="section-head">
                <div>
                    <span class="section-label">FEATURED</span>
                    <h2>Популярні товари</h2>
                </div>
                <a href="{{ url('/catalog') }}" class="section-link">Дивитися все</a>
            </div>

            <div class="products-grid">
                @if (!empty($data['featuredProducts']))
                    @foreach ($data['featuredProducts'] as $product)
                        <div class="product-card">
                            <div class="product-image">
                                <span class="product-tag">
                                    {{ !empty($product['category_name']) ? $product['category_name'] : 'New' }}
                                </span>

                                @if (!empty($product['image']))
                                    <img
                                        src="{{ asset('assets/images/products/' . $product['image']) }}"
                                        alt="{{ $product['name'] }}"
                                        class="home-product-image"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                    >
                                    <div class="image-placeholder image-fallback" style="display:none;">
                                        {{ $product['name'] }}
                                    </div>
                                @else
                                    <div class="image-placeholder">
                                        {{ $product['name'] }}
                                    </div>
                                @endif
                            </div>

                            <div class="product-info">
                                <h3>{{ $product['name'] }}</h3>
                                @if (! empty($product['old_price']) && (float)$product['old_price'] > (float)$product['price'])
                                    <p class="product-price">
                                        <span class="price-sale">{{ number_format((float)$product['price'], 0, '.', ' ') }} грн</span>
                                        <span class="price-old">{{ number_format((float)$product['old_price'], 0, '.', ' ') }} грн</span>
                                    </p>
                                @else
                                    <p class="product-price">
                                        {{ number_format((float)$product['price'], 0, '.', ' ') }} грн
                                    </p>
                                @endif
                                <a href="{{ url('/product/' . $product['id']) }}" class="btn btn-small">
                                    Детальніше
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-box">
                        <h3>Поки що немає товарів</h3>
                        <p>Додай товари в базу даних, і вони з’являться тут.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="banner-section">
        <div class="container">
            <div class="banner-box">
                <div>
                    <span class="section-label">COLLECTION</span>
                    <h2>Онови свій гардероб вже сьогодні</h2>
                    <p>
                        Підбери речі, які підкреслять твій стиль та зроблять
                        магазин виглядом реально сучасним уже з головної сторінки.
                    </p>
                </div>
                <a href="{{ url('/catalog') }}" class="btn btn-dark">До покупок</a>
            </div>
        </div>
    </section>
</main>

@endsection