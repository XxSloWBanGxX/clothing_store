@extends('layouts.app')

@section('title', 'Каталог товарів - CLOTHSTORE')

@section('content')
@php
    $filters = $data['filters'] ?? [];
    $products = $data['products'] ?? [];
    $categories = $data['categories'] ?? [];
    $total = $data['totalProducts'] ?? count($products);

    $catalogQuery = function (array $overrides = [], array $remove = []) use ($filters) {
        $params = [];
        $keys = ['search', 'category', 'min_price', 'max_price', 'sort', 'size', 'color'];

        foreach ($keys as $key) {
            if (in_array($key, $remove, true)) {
                continue;
            }
            $value = array_key_exists($key, $overrides) ? $overrides[$key] : ($filters[$key] ?? '');
            if ($value !== '' && $value !== null) {
                $params[$key] = $value;
            }
        }

        $inStock = array_key_exists('in_stock', $overrides)
            ? (bool) $overrides['in_stock']
            : ! empty($filters['in_stock']);

        if ($inStock && ! in_array('in_stock', $remove, true)) {
            $params['in_stock'] = 1;
        }

        if (! isset($params['sort']) || $params['sort'] === '') {
            $params['sort'] = 'newest';
        }

        $query = http_build_query($params);

        return url('/catalog') . ($query !== '' ? '?' . $query : '');
    };

    $activeChips = [];
    if (! empty($filters['search'])) {
        $activeChips[] = ['label' => 'Пошук: ' . $filters['search'], 'remove' => ['search']];
    }
    if (! empty($filters['category'])) {
        $catName = collect($categories)->firstWhere('slug', $filters['category'])['name'] ?? $filters['category'];
        $activeChips[] = ['label' => $catName, 'remove' => ['category']];
    }
    if ($filters['min_price'] ?? '') {
        $activeChips[] = ['label' => 'Від ' . $filters['min_price'] . ' грн', 'remove' => ['min_price']];
    }
    if ($filters['max_price'] ?? '') {
        $activeChips[] = ['label' => 'До ' . $filters['max_price'] . ' грн', 'remove' => ['max_price']];
    }
    if (! empty($filters['size'])) {
        $activeChips[] = ['label' => 'Розмір: ' . $filters['size'], 'remove' => ['size']];
    }
    if (! empty($filters['color'])) {
        $activeChips[] = ['label' => 'Колір: ' . $filters['color'], 'remove' => ['color']];
    }
    if (! empty($filters['in_stock'])) {
        $activeChips[] = ['label' => 'В наявності', 'remove' => ['in_stock']];
    }
@endphp

<main class="catalog-page catalog-page-v2">
    <section class="catalog-top">
        <div class="container">
            <nav class="catalog-breadcrumbs" aria-label="Навігація">
                <a href="{{ url('/') }}">Головна</a>
                <span>/</span>
                <span>Каталог</span>
            </nav>

            <div class="catalog-top-copy">
                <span class="section-label">SHOP</span>
                <h1>Каталог одягу</h1>
                <p>Підбери речі за категорією, ціною, розміром і кольором — все в одному місці.</p>
            </div>

            @if (! empty($categories))
                <div class="catalog-cat-rail-wrap">
                    <div class="catalog-cat-rail" role="tablist" aria-label="Категорії">
                        <a href="{{ $catalogQuery([], ['category']) }}"
                           class="catalog-cat-pill {{ empty($filters['category']) ? 'is-active' : '' }}">
                            Усі
                        </a>
                        @foreach ($categories as $category)
                            <a href="{{ $catalogQuery(['category' => $category['slug']]) }}"
                               class="catalog-cat-pill {{ (($filters['category'] ?? '') === $category['slug']) ? 'is-active' : '' }}">
                                {{ $category['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    @if (session('success'))
        <div class="container">
            <div class="alert-success catalog-alert">{{ session('success') }}</div>
        </div>
    @endif

    @if (session('stockError'))
        <div class="container">
            <div class="alert-error catalog-alert">{{ session('stockError') }}</div>
        </div>
    @endif

    <section class="catalog-section-v2">
        <div class="container catalog-shell">
            <div class="catalog-overlay" id="catalogOverlay" hidden></div>

            <aside class="catalog-panel" id="catalogPanel" aria-label="Фільтри">
                <div class="catalog-panel-head">
                    <div>
                        <h2>Фільтри</h2>
                        <p>Уточни пошук</p>
                    </div>
                    <button type="button" class="catalog-panel-close" id="catalogPanelClose" aria-label="Закрити фільтри">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="catalog-panel-scroll">
                <form method="GET" action="{{ url('/catalog') }}" class="catalog-filter-form" id="catalogFilterForm">
                    <input type="hidden" name="category" value="{{ $filters['category'] ?? '' }}">

                    <div class="catalog-filter-block">
                        <label class="catalog-filter-label" for="search">Пошук</label>
                        <div class="catalog-input-wrap">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input type="text" id="search" name="search" placeholder="Назва товару..." value="{{ $filters['search'] ?? '' }}">
                        </div>
                    </div>

                    <div class="catalog-filter-block">
                        <span class="catalog-filter-label">Ціна, грн</span>
                        <div class="catalog-price-row">
                            <input type="number" id="min_price" name="min_price" placeholder="Від" value="{{ $filters['min_price'] ?? '' }}" min="0">
                            <span class="catalog-price-sep">—</span>
                            <input type="number" id="max_price" name="max_price" placeholder="До" value="{{ $filters['max_price'] ?? '' }}" min="0">
                        </div>
                        <input type="range" id="max_price_range" class="catalog-range" min="0" max="5000" step="100" value="{{ $filters['max_price'] ?? 5000 }}">
                        <div class="catalog-range-value">До <span id="maxPriceValue">{{ $filters['max_price'] ?? 5000 }}</span> грн</div>
                    </div>

                    @if (! empty($data['allSizes']))
                        <div class="catalog-filter-block">
                            <span class="catalog-filter-label">Розмір</span>
                            <div class="catalog-chip-group">
                                @foreach ($data['allSizes'] as $sizeOption)
                                    <label class="catalog-chip-option">
                                        <input type="radio" name="size" value="{{ $sizeOption }}" {{ (($filters['size'] ?? '') === $sizeOption) ? 'checked' : '' }}>
                                        <span>{{ $sizeOption }}</span>
                                    </label>
                                @endforeach
                                <label class="catalog-chip-option">
                                    <input type="radio" name="size" value="" {{ empty($filters['size']) ? 'checked' : '' }}>
                                    <span>Усі</span>
                                </label>
                            </div>
                        </div>
                    @endif

                    @if (! empty($data['allColors']))
                        <div class="catalog-filter-block">
                            <label class="catalog-filter-label" for="color">Колір</label>
                            <select id="color" name="color">
                                <option value="">Усі кольори</option>
                                @foreach ($data['allColors'] as $colorOption)
                                    <option value="{{ $colorOption }}" {{ (($filters['color'] ?? '') === $colorOption) ? 'selected' : '' }}>
                                        {{ $colorOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <input type="hidden" name="sort" id="catalogSortInput" value="{{ $filters['sort'] ?? 'newest' }}">

                    <label class="catalog-check">
                        <input type="checkbox" name="in_stock" value="1" {{ ! empty($filters['in_stock']) ? 'checked' : '' }}>
                        <span>Тільки в наявності</span>
                    </label>

                    <div class="catalog-filter-actions">
                        <button type="submit" class="btn btn-dark">Застосувати</button>
                        <a href="{{ url('/catalog') }}" class="btn btn-light">Скинути</a>
                    </div>
                </form>
                </div>
            </aside>

            <div class="catalog-results">
                <div class="catalog-toolbar-v2">
                    <div class="catalog-toolbar-left">
                        <button type="button" class="catalog-filter-toggle btn btn-light" id="catalogFilterToggle">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                            Фільтри
                        </button>
                        <p class="catalog-count">Знайдено <strong>{{ $total }}</strong></p>
                    </div>

                    <div class="catalog-toolbar-right">
                        <div class="catalog-sort-group">
                            <span class="catalog-sort-label">Сортування</span>
                            <div class="catalog-sort-pills" role="group" aria-label="Сортування">
                                @foreach ([
                                    'newest' => 'Новинки',
                                    'price_asc' => 'Дешевші',
                                    'price_desc' => 'Дорожчі',
                                    'name_asc' => 'А → Я',
                                ] as $sortValue => $sortLabel)
                                    <button type="button"
                                            class="catalog-sort-pill {{ (($filters['sort'] ?? 'newest') === $sortValue) ? 'is-active' : '' }}"
                                            data-sort="{{ $sortValue }}">
                                        {{ $sortLabel }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                @if (! empty($activeChips))
                    <div class="catalog-active-filters">
                        @foreach ($activeChips as $chip)
                            <a href="{{ $catalogQuery([], $chip['remove']) }}" class="catalog-active-chip">
                                {{ $chip['label'] }}
                                <span aria-hidden="true">×</span>
                            </a>
                        @endforeach
                        <a href="{{ url('/catalog') }}" class="catalog-clear-all">Очистити все</a>
                    </div>
                @endif

                @if (! empty($products))
                    <div class="catalog-grid-v2">
                        @foreach ($products as $product)
                            @php
                                $onSale = ! empty($product['on_sale']);
                                $inStock = (int) ($product['stock'] ?? 0) > 0;
                            @endphp
                            <article class="catalog-item">
                                <div class="catalog-item-media">
                                    <div class="catalog-item-quick">
                                        @include('partials.favorite-folder-picker', [
                                            'productId' => $product['id'],
                                            'folders' => $data['favoriteFolders'] ?? [],
                                        ])
                                        @if ($inStock)
                                            <form action="{{ url('/cart/add') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                                <button type="submit" class="catalog-quick-btn" title="Додати в кошик">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <a href="{{ url('/product/' . $product['id']) }}" class="catalog-item-link" tabindex="-1" aria-hidden="true">
                                        @if ($onSale)
                                            <span class="catalog-item-badge sale">Sale</span>
                                        @endif

                                        @if (! empty($product['image']))
                                            <img
                                                src="{{ asset('assets/images/products/' . $product['image']) }}"
                                                alt="{{ $product['name'] }}"
                                                class="catalog-item-image"
                                                loading="lazy"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                            >
                                            <div class="catalog-item-fallback" style="display:none;">{{ $product['name'] }}</div>
                                        @else
                                            <div class="catalog-item-fallback">{{ $product['name'] }}</div>
                                        @endif
                                    </a>
                                </div>

                                <div class="catalog-item-body">
                                    @if (! empty($product['category_name']))
                                        <span class="catalog-item-cat">{{ $product['category_name'] }}</span>
                                    @endif
                                    <h3 class="catalog-item-title">
                                        <a href="{{ url('/product/' . $product['id']) }}">{{ $product['name'] }}</a>
                                    </h3>

                                    <div class="catalog-item-meta">
                                        @if ($onSale)
                                            <p class="catalog-item-price">
                                                <span class="price-sale">{{ number_format((float) $product['price'], 0, '.', ' ') }} грн</span>
                                                <span class="price-old">{{ number_format((float) $product['old_price'], 0, '.', ' ') }} грн</span>
                                            </p>
                                        @else
                                            <p class="catalog-item-price">{{ number_format((float) $product['price'], 0, '.', ' ') }} грн</p>
                                        @endif

                                        <span class="catalog-item-stock {{ $inStock ? 'in' : 'out' }}">
                                            {{ $inStock ? 'В наявності' : 'Немає' }}
                                        </span>
                                    </div>

                                    <a href="{{ url('/product/' . $product['id']) }}" class="catalog-item-more">Детальніше →</a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if (isset($data['paginator']) && $data['paginator']->hasPages())
                        <div class="catalog-pagination-wrap">
                            {{ $data['paginator']->onEachSide(1)->links('vendor.pagination.custom') }}
                        </div>
                    @endif
                @else
                    <div class="catalog-empty">
                        <div class="catalog-empty-icon">🔍</div>
                        <h3>Товарів не знайдено</h3>
                        <p>Спробуй змінити фільтри або обрати іншу категорію.</p>
                        <a href="{{ url('/catalog') }}" class="btn btn-dark">Скинути фільтри</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</main>

<script src="{{ asset('assets/js/catalog.js') }}"></script>
@endsection
