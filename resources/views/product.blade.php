@extends('layouts.app')

@php
    $product = $data['product'];
    $images = $data['images'] ?? [];
    $sizes = $data['sizes'] ?? [];
    $colors = $data['colors'] ?? [];
    $reviews = $data['reviews'] ?? [];
    $avgRating = $data['avgRating'] ?? 0;
    $reviewCount = count($reviews);

    $gallery = [];
    if (! empty($product['image'])) {
        $gallery[] = $product['image'];
    }
    if (! empty($images)) {
        foreach ($images as $img) {
            if (! empty($img['image_path']) && ! in_array($img['image_path'], $gallery, true)) {
                $gallery[] = $img['image_path'];
            }
        }
    }

    $mainImage = ! empty($gallery) ? $gallery[0] : '';
    $inStock = (int) ($product['stock'] ?? 0) > 0;
    $onSale = ! empty($product['on_sale']);
    $discountPercent = (int) ($product['discount_percent'] ?? 0);

    $characteristics = [
        'Категорія' => $product['category_name'] ?? 'Одяг',
        'Артикул' => ! empty($product['id']) ? ('#' . (int) $product['id']) : '—',
        'Наявність' => $inStock ? 'Є в наявності' : 'Немає в наявності',
    ];

    if (! empty($sizes)) {
        $characteristics['Розміри'] = collect($sizes)->pluck('size_label')->implode(', ');
    }
    if (! empty($colors)) {
        $characteristics['Кольори'] = collect($colors)->pluck('color_name')->implode(', ');
    }
@endphp

@section('title', $product['name'] . ' - CLOTHSTORE')

@section('content')
<main class="product-page product-page-v2">
    <section class="pd-top">
        <div class="container">
            <nav class="pd-breadcrumbs" aria-label="Навігація">
                <a href="{{ url('/') }}">Головна</a>
                <span>/</span>
                <a href="{{ url('/catalog') }}">Каталог</a>
                @if (! empty($product['category_name']))
                    <span>/</span>
                    <a href="{{ url('/catalog?category=' . ($product['category_slug'] ?? '')) }}">{{ $product['category_name'] }}</a>
                @endif
                <span>/</span>
                <span>{{ $product['name'] }}</span>
            </nav>
        </div>
    </section>

    <section class="pd-hero">
        <div class="container pd-layout">
            <div class="pd-gallery">
                <div class="pd-gallery-card">
                    @if (! empty($gallery))
                        <div class="pd-gallery-counter" id="galleryCounter">1 / {{ count($gallery) }}</div>

                        @if ($onSale)
                            <span class="pd-gallery-badge sale">−{{ $discountPercent }}%</span>
                        @endif

                        <div class="pd-gallery-main">
                            <button type="button" class="pd-gallery-arrow left" id="productPrevImage" aria-label="Попереднє фото">‹</button>

                            <img
                                id="mainProductImage"
                                src="{{ asset('assets/images/products/' . $mainImage) }}"
                                alt="{{ $product['name'] }}"
                                class="product-gallery-main-image pd-gallery-image"
                                data-current-index="0"
                                onerror="this.style.display='none'; document.getElementById('mainProductFallback').style.display='flex';"
                            >
                            <div id="mainProductFallback" class="pd-gallery-fallback" style="display:none;">{{ $product['name'] }}</div>

                            <button type="button" class="pd-gallery-arrow right" id="productNextImage" aria-label="Наступне фото">›</button>
                        </div>

                        @if (count($gallery) > 1)
                            <div class="pd-gallery-thumbs">
                                @foreach ($gallery as $index => $imgPath)
                                    <button
                                        type="button"
                                        class="product-gallery-thumb pd-gallery-thumb {{ $index === 0 ? 'active' : '' }}"
                                        data-index="{{ $index }}"
                                        data-image-src="{{ asset('assets/images/products/' . $imgPath) }}"
                                        aria-label="Фото {{ $index + 1 }}"
                                    >
                                        <img src="{{ asset('assets/images/products/' . $imgPath) }}" alt="" class="product-gallery-thumb-image">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="pd-gallery-main">
                            <div class="pd-gallery-fallback visible">{{ $product['name'] }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="pd-buy">
                <div class="pd-buy-card">
                    <div class="pd-buy-head">
                        @if (! empty($product['category_name']))
                            <a href="{{ url('/catalog?category=' . ($product['category_slug'] ?? '')) }}" class="pd-category">{{ $product['category_name'] }}</a>
                        @endif
                        <span class="pd-stock {{ $inStock ? 'in' : 'out' }}">{{ $inStock ? 'В наявності' : 'Немає в наявності' }}</span>
                    </div>

                    <h1 class="pd-title">{{ $product['name'] }}</h1>

                    <div class="pd-meta-row">
                        <span class="pd-sku">Артикул #{{ $product['id'] }}</span>
                        @if ($reviewCount > 0)
                            <div class="pd-rating">
                                <span class="pd-rating-stars">{!! str_repeat('★', (int) round($avgRating)) . str_repeat('☆', 5 - (int) round($avgRating)) !!}</span>
                                <span>{{ $avgRating }} · {{ $reviewCount }} {{ $reviewCount === 1 ? 'відгук' : ($reviewCount < 5 ? 'відгуки' : 'відгуків') }}</span>
                            </div>
                        @endif
                    </div>

                    @if (session('success'))
                        <div class="alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('stockError'))
                        <div class="alert-error">{{ session('stockError') }}</div>
                    @endif

                    <div class="pd-price-block">
                        @if ($onSale)
                            <div class="pd-price-row">
                                <span class="pd-price-current">{{ number_format((float) $product['price'], 0, '.', ' ') }} грн</span>
                                <span class="pd-price-old">{{ number_format((float) $product['old_price'], 0, '.', ' ') }} грн</span>
                                <span class="pd-price-badge">Sale</span>
                            </div>
                        @else
                            <div class="pd-price-current solo">{{ number_format((float) $product['price'], 0, '.', ' ') }} грн</div>
                        @endif
                        <p class="pd-price-note">Онлайн-ціна · доставка по Україні</p>
                    </div>

                    <form action="{{ url('/cart/add') }}" method="POST" id="productCartForm" class="pd-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                        <input type="hidden" name="selected_size" id="selectedSizeInput" value="{{ $sizes[0]['size_label'] ?? '' }}">
                        <input type="hidden" name="selected_color_name" id="selectedColorNameInput" value="{{ $colors[0]['color_name'] ?? '' }}">
                        <input type="hidden" name="selected_color_hex" id="selectedColorHexInput" value="{{ $colors[0]['color_hex'] ?? '' }}">

                        @if (! empty($sizes))
                            <div class="pd-option">
                                <div class="pd-option-head">
                                    <span class="pd-option-label">Розмір</span>
                                    <span class="pd-option-hint" id="selectedSizeLabel">{{ $sizes[0]['size_label'] ?? '' }}</span>
                                </div>
                                <div class="pd-size-list">
                                    @foreach ($sizes as $index => $size)
                                        <button type="button" class="product-size-btn pd-size-btn {{ $index === 0 ? 'active' : '' }}" data-size="{{ $size['size_label'] }}">
                                            {{ $size['size_label'] }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (! empty($colors))
                            <div class="pd-option">
                                <div class="pd-option-head">
                                    <span class="pd-option-label">Колір</span>
                                    <span class="pd-option-hint" id="selectedColorLabel">{{ $colors[0]['color_name'] ?? '' }}</span>
                                </div>
                                <div class="pd-color-list">
                                    @foreach ($colors as $index => $color)
                                        @php
                                            $buttonColor = '#d9d9df';
                                            if (! empty($color['color_hex'])) {
                                                $buttonColor = str_starts_with($color['color_hex'], '#') ? $color['color_hex'] : ('#' . $color['color_hex']);
                                            }
                                        @endphp
                                        <button
                                            type="button"
                                            class="product-color-btn pd-color-btn {{ $index === 0 ? 'active' : '' }}"
                                            data-color-name="{{ $color['color_name'] }}"
                                            data-color-hex="{{ $color['color_hex'] ?? '' }}"
                                            title="{{ $color['color_name'] }}"
                                        >
                                            <span class="product-color-dot pd-color-dot" data-color="{{ $buttonColor }}"></span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($inStock)
                            <div class="pd-option">
                                <div class="pd-option-head">
                                    <span class="pd-option-label">Кількість</span>
                                </div>
                                <div class="product-qty-control pd-qty">
                                    <button type="button" class="product-qty-btn" id="qtyMinus" aria-label="Менше">−</button>
                                    <input type="number" name="quantity" id="qtyInput" value="1" min="1" max="{{ (int) $product['stock'] }}" readonly>
                                    <button type="button" class="product-qty-btn" id="qtyPlus" aria-label="Більше">+</button>
                                </div>
                            </div>
                        @endif

                        <div class="pd-actions">
                            @if ($inStock)
                                <button type="submit" class="btn btn-dark pd-cart-btn">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                    Додати в кошик
                                </button>
                            @endif
                        </div>
                    </form>

                    @if (! $inStock)
                        <div class="pd-notify-card">
                            <div class="pd-notify-card-head">
                                <span class="pd-notify-icon" aria-hidden="true">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                                </span>
                                <div>
                                    <span class="pd-notify-label">BACK IN STOCK</span>
                                    <h3 class="pd-notify-title">Повідомити, коли зʼявиться</h3>
                                    <p class="pd-notify-text">Товар зараз недоступний. Залиш email — надішлемо лист одразу після поповнення складу.</p>
                                </div>
                            </div>
                            <form action="{{ url('/product/' . $product['id'] . '/stock-alert') }}" method="POST" class="pd-notify-form">
                                @csrf
                                <div class="pd-notify-field">
                                    <span class="pd-notify-field-icon" aria-hidden="true">@</span>
                                    <input type="email" name="email" id="stockAlertEmail" placeholder="Ваш email" value="{{ auth()->user()->email ?? '' }}" required autocomplete="email">
                                    <button type="submit" class="pd-notify-submit">
                                        <span>Повідомити</span>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    @include('partials.favorite-folder-picker', [
                        'productId' => $product['id'],
                        'folders' => $data['favoriteFolders'] ?? [],
                        'variant' => 'product',
                    ])

                    <div class="pd-trust">
                        <div class="pd-trust-item">
                            <span class="pd-trust-icon">🚚</span>
                            <div>
                                <strong>Доставка</strong>
                                <p>{{ $site['shipping_info'] ?? 'Нова Пошта, Укрпошта, Meest' }}</p>
                            </div>
                        </div>
                        <div class="pd-trust-item">
                            <span class="pd-trust-icon">💳</span>
                            <div>
                                <strong>Оплата</strong>
                                <p>{{ $site['trust_payment_text'] ?? 'Онлайн або при отриманні' }}</p>
                            </div>
                        </div>
                        <div class="pd-trust-item">
                            <span class="pd-trust-icon">↩</span>
                            <div>
                                <strong>Повернення</strong>
                                <p>{{ $site['returns_info'] ?? '14 днів без зайвих питань' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pd-details">
        <div class="container">
            <div class="pd-tabs-card">
                <div class="pd-tabs-nav">
                    <button type="button" class="product-tab-btn pd-tab-btn active" data-tab="description">Опис</button>
                    <button type="button" class="product-tab-btn pd-tab-btn" data-tab="characteristics">Характеристики</button>
                    <button type="button" class="product-tab-btn pd-tab-btn" data-tab="reviews">
                        Відгуки
                        @if ($reviewCount > 0)
                            <span class="pd-tab-count">{{ $reviewCount }}</span>
                        @endif
                    </button>
                </div>

                <div class="pd-tab-panels">
                    <section class="product-tab-panel pd-tab-panel active" data-panel="description">
                        <h2>Про товар</h2>
                        <div class="pd-description">
                            {!! nl2br(e($product['description'] ?? 'Опис товару буде додано пізніше.')) !!}
                        </div>
                    </section>

                    <section class="product-tab-panel pd-tab-panel" data-panel="characteristics">
                        <h2>Характеристики</h2>
                        <dl class="pd-specs">
                            @foreach ($characteristics as $label => $value)
                                <div class="pd-spec-row">
                                    <dt>{{ $label }}</dt>
                                    <dd>{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>

                    <section class="product-tab-panel pd-tab-panel" data-panel="reviews">
                        <div class="pd-reviews-head">
                            <div>
                                <h2>Відгуки покупців</h2>
                                @if ($reviewCount > 0)
                                    <p class="pd-reviews-summary">{{ $avgRating }} з 5 · {{ $reviewCount }} оцінок</p>
                                @else
                                    <p class="pd-reviews-summary">Поки без відгуків — будь першим</p>
                                @endif
                            </div>
                            @if ($reviewCount > 0)
                                <div class="pd-reviews-score">
                                    <strong>{{ $avgRating }}</strong>
                                    <span class="pd-rating-stars">{!! str_repeat('★', (int) round($avgRating)) . str_repeat('☆', 5 - (int) round($avgRating)) !!}</span>
                                </div>
                            @endif
                        </div>

                        @if (session('reviewSuccess'))
                            <div class="alert-success">{{ session('reviewSuccess') }}</div>
                        @endif

                        @if (! empty($reviews))
                            <div class="pd-reviews-list">
                                @foreach ($reviews as $review)
                                    <article class="pd-review">
                                        <div class="pd-review-top">
                                            <strong>{{ $review['author_name'] }}</strong>
                                            <span class="pd-rating-stars sm">{!! str_repeat('★', (int) $review['rating']) . str_repeat('☆', 5 - (int) $review['rating']) !!}</span>
                                        </div>
                                        <p>{{ $review['comment'] }}</p>
                                        <time class="pd-review-date">{{ $review['created_at'] }}</time>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="pd-reviews-empty">Ще ніхто не залишив відгук про цей товар.</div>
                        @endif

                        @auth
                            <form action="{{ url('/product/' . $product['id'] . '/review') }}" method="POST" class="pd-review-form">
                                @csrf
                                <h3>Написати відгук</h3>
                                <div class="pd-review-fields">
                                    <div class="form-group">
                                        <label for="rating">Оцінка</label>
                                        <select id="rating" name="rating">
                                            <option value="5">★★★★★ — Відмінно</option>
                                            <option value="4">★★★★☆ — Добре</option>
                                            <option value="3">★★★☆☆ — Нормально</option>
                                            <option value="2">★★☆☆☆ — Погано</option>
                                            <option value="1">★☆☆☆☆ — Жахливо</option>
                                        </select>
                                        @error('rating')<small class="form-error">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="comment">Коментар</label>
                                        <textarea id="comment" name="comment" rows="4" placeholder="Що сподобалось? Як сидить?">{{ old('comment') }}</textarea>
                                        @error('comment')<small class="form-error">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-dark">Надіслати відгук</button>
                            </form>
                        @else
                            <p class="pd-reviews-login"><a href="{{ url('/login') }}">Увійди</a>, щоб залишити відгук.</p>
                        @endauth
                    </section>
                </div>
            </div>
        </div>
    </section>

    @include('partials.recently-viewed', [
        'products' => $data['recentlyViewed'] ?? [],
        'title' => 'Нещодавно переглянуті',
    ])

    @if (! empty($data['related']))
        <section class="pd-related">
            <div class="container">
                <div class="section-head">
                    <div>
                        <span class="section-label">YOU MAY LIKE</span>
                        <h2>Схожі товари</h2>
                    </div>
                    <a href="{{ url('/catalog?category=' . ($product['category_slug'] ?? '')) }}" class="section-link">Уся категорія →</a>
                </div>

                <div class="catalog-grid-v2">
                    @foreach ($data['related'] as $rel)
                        @php
                            $relSale = ! empty($rel['on_sale']);
                            $relStock = (int) ($rel['stock'] ?? 0) > 0;
                        @endphp
                        <article class="catalog-item">
                            <div class="catalog-item-media">
                                <a href="{{ url('/product/' . (int) $rel['id']) }}" class="catalog-item-link" tabindex="-1" aria-hidden="true">
                                    @if ($relSale)
                                        <span class="catalog-item-badge sale">Sale</span>
                                    @endif
                                    @if (! empty($rel['image']))
                                        <img src="{{ asset('assets/images/products/' . $rel['image']) }}" alt="{{ $rel['name'] }}" class="catalog-item-image" loading="lazy">
                                    @else
                                        <div class="catalog-item-fallback">{{ $rel['name'] }}</div>
                                    @endif
                                </a>
                            </div>
                            <div class="catalog-item-body">
                                <h3 class="catalog-item-title">
                                    <a href="{{ url('/product/' . (int) $rel['id']) }}">{{ $rel['name'] }}</a>
                                </h3>
                                <div class="catalog-item-meta">
                                    @if ($relSale)
                                        <p class="catalog-item-price">
                                            <span class="price-sale">{{ number_format((float) $rel['price'], 0, '.', ' ') }} грн</span>
                                            <span class="price-old">{{ number_format((float) $rel['old_price'], 0, '.', ' ') }} грн</span>
                                        </p>
                                    @else
                                        <p class="catalog-item-price">{{ number_format((float) $rel['price'], 0, '.', ' ') }} грн</p>
                                    @endif
                                    <span class="catalog-item-stock {{ $relStock ? 'in' : 'out' }}">{{ $relStock ? 'В наявності' : 'Немає' }}</span>
                                </div>
                                <a href="{{ url('/product/' . (int) $rel['id']) }}" class="catalog-item-more">Детальніше →</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($inStock)
        <div class="pd-mobile-bar">
            <div class="pd-mobile-bar-inner container">
                <div class="pd-mobile-price">{{ number_format((float) $product['price'], 0, '.', ' ') }} грн</div>
                <button type="submit" form="productCartForm" class="btn btn-dark">В кошик</button>
            </div>
        </div>
    @endif
</main>

<script src="{{ asset('assets/js/product.js') }}"></script>
@endsection
