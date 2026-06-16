@extends('layouts.app')

@section('title', 'Головна - CLOTHSTORE')

@section('content')

<main>
    <section class="hero-section">
        <div class="container hero-grid">
            <div class="hero-content">
                <span class="hero-badge">{{ $site['hero_badge'] ?? 'NEW COLLECTION' }}</span>
                <h1>{!! nl2br(e($site['hero_title'] ?? 'Стиль, який говорить за тебе')) !!}</h1>
                <p>{{ $site['hero_text'] ?? '' }}</p>

                <div class="hero-buttons">
                    <a href="{{ url($site['hero_btn1_url'] ?? '/catalog') }}" class="btn btn-dark">{{ $site['hero_btn1_text'] ?? 'Перейти в каталог' }}</a>
                    <a href="{{ url($site['hero_btn2_url'] ?? '/new') }}" class="btn btn-light">{{ $site['hero_btn2_text'] ?? 'Дивитися новинки' }}</a>
                </div>

                <div class="hero-stats">
                    <div class="stat-item">
                        <h3>{{ $site['hero_stat1_value'] ?? '500+' }}</h3>
                        <p>{{ $site['hero_stat1_label'] ?? 'Товарів' }}</p>
                    </div>
                    <div class="stat-item">
                        <h3>{{ $site['hero_stat2_value'] ?? '24/7' }}</h3>
                        <p>{{ $site['hero_stat2_label'] ?? 'Онлайн замовлення' }}</p>
                    </div>
                    <div class="stat-item">
                        <h3>{{ $site['hero_stat3_value'] ?? '100%' }}</h3>
                        <p>{{ $site['hero_stat3_label'] ?? 'Сучасний стиль' }}</p>
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
                <h3>{{ $site['feature1_title'] ?? '' }}</h3>
                <p>{{ $site['feature1_text'] ?? '' }}</p>
            </div>
            <div class="feature-box">
                <h3>{{ $site['feature2_title'] ?? '' }}</h3>
                <p>{{ $site['feature2_text'] ?? '' }}</p>
            </div>
            <div class="feature-box">
                <h3>{{ $site['feature3_title'] ?? '' }}</h3>
                <p>{{ $site['feature3_text'] ?? '' }}</p>
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
                                @include('partials.product-price', ['product' => $product])
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
                    <span class="section-label">{{ $site['banner_label'] ?? 'COLLECTION' }}</span>
                    <h2>{{ $site['banner_title'] ?? '' }}</h2>
                    <p>{{ $site['banner_text'] ?? '' }}</p>
                </div>
                <a href="{{ url($site['banner_btn_url'] ?? '/catalog') }}" class="btn btn-dark">{{ $site['banner_btn_text'] ?? 'До покупок' }}</a>
            </div>
        </div>
    </section>
</main>

@endsection