@extends('layouts.app')

@section('title', 'Каталог товарів - CLOTHSTORE')

@section('content')
<main class="catalog-page">
    <section class="catalog-hero">
        <div class="container">
            <div class="catalog-hero-box">
                <span class="hero-badge">CATALOG</span>
                <h1>Каталог товарів</h1>
                <p>Знайди потрібний товар через фільтри, категорії та зручний перегляд.</p>
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="container">
            <div class="alert-success">{{ session('success') }}</div>
        </div>
    @endif

    @if (session('stockError'))
        <div class="container">
            <div class="alert-error">{{ session('stockError') }}</div>
        </div>
    @endif

    <section class="catalog-section">
        <div class="container catalog-layout">
            <aside class="catalog-sidebar">
                <form method="GET" action="{{ url('/catalog') }}" class="filter-box">
                    
                    <div class="filter-group">
                        <label for="search">Пошук</label>
                        <input type="text" id="search" name="search" placeholder="Назва товару..." value="{{ $data['filters']['search'] ?? '' }}">
                    </div>

                    <div class="filter-group">
                        <label for="category">Категорія</label>
                        <select id="category" name="category">
                            <option value="">Усі категорії</option>
                            @foreach ($data['categories'] ?? [] as $category)
                                <option value="{{ $category['slug'] }}" {{ (($data['filters']['category'] ?? '') === $category['slug']) ? 'selected' : '' }}>
                                    {{ $category['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-grid-two">
                        <div class="filter-group">
                            <label for="min_price">Ціна від</label>
                            <input type="number" id="min_price" name="min_price" placeholder="0" value="{{ $data['filters']['min_price'] ?? '' }}">
                        </div>

                        <div class="filter-group">
                            <label for="max_price">Ціна до</label>
                            <input type="number" id="max_price" name="max_price" placeholder="5000" value="{{ $data['filters']['max_price'] ?? '' }}">
                        </div>
                    </div>

                    <div class="filter-group">
                        <label for="max_price_range">Повзунок ціни до</label>
                        <input type="range" id="max_price_range" min="0" max="5000" step="100" value="{{ $data['filters']['max_price'] ?? 5000 }}">
                        <div class="range-value">
                            До: <span id="maxPriceValue">{{ $data['filters']['max_price'] ?? 5000 }}</span> грн
                        </div>
                    </div>

                    @if (! empty($data['allSizes']))
                        <div class="filter-group">
                            <label for="size">Розмір</label>
                            <select id="size" name="size">
                                <option value="">Усі розміри</option>
                                @foreach ($data['allSizes'] as $sizeOption)
                                    <option value="{{ $sizeOption }}" {{ (($data['filters']['size'] ?? '') === $sizeOption) ? 'selected' : '' }}>
                                        {{ $sizeOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if (! empty($data['allColors']))
                        <div class="filter-group">
                            <label for="color">Колір</label>
                            <select id="color" name="color">
                                <option value="">Усі кольори</option>
                                @foreach ($data['allColors'] as $colorOption)
                                    <option value="{{ $colorOption }}" {{ (($data['filters']['color'] ?? '') === $colorOption) ? 'selected' : '' }}>
                                        {{ $colorOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="filter-group">
                        <label for="sort">Сортування</label>
                        <select id="sort" name="sort">
                            <option value="newest" {{ (($data['filters']['sort'] ?? '') === 'newest') ? 'selected' : '' }}>Спочатку нові</option>
                            <option value="price_asc" {{ (($data['filters']['sort'] ?? '') === 'price_asc') ? 'selected' : '' }}>Ціна: від дешевих</option>
                            <option value="price_desc" {{ (($data['filters']['sort'] ?? '') === 'price_desc') ? 'selected' : '' }}>Ціна: від дорогих</option>
                            <option value="name_asc" {{ (($data['filters']['sort'] ?? '') === 'name_asc') ? 'selected' : '' }}>За назвою</option>
                        </select>
                    </div>

                    <div class="filter-checkbox">
                        <label>
                            <input type="checkbox" name="in_stock" value="1" {{ !empty($data['filters']['in_stock']) ? 'checked' : '' }}>
                            Тільки в наявності
                        </label>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-dark">Застосувати</button>
                        <a href="{{ url('/catalog') }}" class="btn btn-light">Скинути</a>
                    </div>
                </form>
            </aside>

            <div class="catalog-content">
                <div class="catalog-toolbar">
                    <div>
                        <h2>Товари</h2>
                        <p>Знайдено: {{ $data['totalProducts'] ?? count($data['products'] ?? []) }}</p>
                    </div>
                </div>

                @if (!empty($data['products']))
                    <div class="catalog-grid">
                        @foreach ($data['products'] as $product)
                            <div class="catalog-card">
                                <a href="{{ url('/product/' . $product['id']) }}" class="catalog-card-link">
                                    <div class="catalog-card-image">
                                        @if (! empty($product['old_price']) && (float)$product['old_price'] > (float)$product['price'])
                                            <span class="sale-badge">SALE</span>
                                        @endif
                                        @if (!empty($product['image']))
                                            <img
                                                src="{{ asset('assets/images/products/' . $product['image']) }}"
                                                alt="{{ $product['name'] }}"
                                                class="catalog-product-image"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                            >
                                            <div class="product-image-placeholder image-fallback" style="display:none;">
                                                {{ $product['name'] }}
                                            </div>
                                        @else
                                            <div class="product-image-placeholder">
                                                {{ $product['name'] }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="catalog-card-info">
                                        <h3>{{ $product['name'] }}</h3>
                                        @if (! empty($product['old_price']) && (float)$product['old_price'] > (float)$product['price'])
                                            <p class="catalog-card-price">
                                                <span class="price-sale">{{ number_format((float)$product['price'], 0, '.', ' ') }} грн</span>
                                                <span class="price-old">{{ number_format((float)$product['old_price'], 0, '.', ' ') }} грн</span>
                                            </p>
                                        @else
                                            <p class="catalog-card-price">{{ number_format((float)$product['price'], 0, '.', ' ') }} грн</p>
                                        @endif
                                        <p class="catalog-stock {{ (int)$product['stock'] > 0 ? 'in-stock' : 'out-of-stock' }}">
                                            {{ (int)$product['stock'] > 0 ? 'Є в наявності' : 'Немає в наявності' }}
                                        </p>
                                    </div>
                                </a>

                                <div class="catalog-card-menu">
                                    <details class="card-menu">
                                        <summary>•••</summary>
                                        <div class="card-menu-dropdown">
                                            <form action="{{ url('/cart/add') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                                <button type="submit">Додати в кошик</button>
                                            </form>

                                            <form action="{{ url('/favorites/add') }}" method="POST" class="favorite-folder-form">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product['id'] }}">

                                                <label for="folder_{{ $product['id'] }}">Папка</label>
                                                <select name="folder" id="folder_{{ $product['id'] }}" class="favorite-folder-select">
                                                    @foreach ($data['favoriteFolders'] ?? [] as $folder)
                                                        <option value="{{ $folder }}">{{ $folder }}</option>
                                                    @endforeach
                                                </select>

                                                <button type="submit">Додати в обране</button>
                                            </form>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if (isset($data['paginator']) && $data['paginator']->hasPages())
                        {{ $data['paginator']->onEachSide(1)->links('vendor.pagination.custom') }}
                    @endif
                @else
                    <div class="empty-box">
                        <h3>Товарів не знайдено</h3>
                        <p>Спробуй змінити фільтри або очистити пошук.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</main>
@endsection