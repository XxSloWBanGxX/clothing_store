@extends('layouts.app')

@php
/** @var array $data */
/** @var array $product */
    $product = $data['product'];
    $images = $data['images'] ?? [];
    $sizes = $data['sizes'] ?? [];
    $colors = $data['colors'] ?? [];

    $gallery = [];
    if (!empty($product['image'])) {
        $gallery[] = $product['image'];
    }
    if (!empty($images)) {
        foreach ($images as $img) {
            if (!empty($img['image_path']) && !in_array($img['image_path'], $gallery, true)) {
                $gallery[] = $img['image_path'];
            }
        }
    }

    $mainImage = !empty($gallery) ? $gallery[0] : '';

    $characteristics = [
        'Категорія' => $product['category_name'] ?? 'Одяг',
        'Артикул' => !empty($product['id']) ? ('#' . (int)$product['id']) : '—',
        'Наявність' => (int)$product['stock'] > 0 ? 'Є в наявності' : 'Немає в наявності',
    ];
@endphp

@section('title', $product['name'] . ' - CLOTHSTORE')

@section('content')
<main class="product-page">
    <section class="product-breadcrumbs-wrap">
        <div class="container">
            <nav class="product-breadcrumbs">
                <a href="{{ url('/') }}">Головна</a>
                <span>/</span>
                <a href="{{ url('/catalog') }}">Каталог</a>
                <span>/</span>
                <span>{{ $product['category_name'] ?? 'Товар' }}</span>
            </nav>
        </div>
    </section>

    <section class="product-market-section">
        <div class="container">
            <div class="product-market-layout">
                <div class="product-gallery-panel">
                    <div class="product-gallery-grid">
                        @if (!empty($gallery))
                            <div class="product-gallery-thumbs">
                                @foreach ($gallery as $index => $imgPath)
                                    <button
                                        type="button"
                                        class="product-gallery-thumb {{ $index === 0 ? 'active' : '' }}"
                                        data-index="{{ $index }}"
                                        data-image-src="{{ asset('assets/images/products/' . $imgPath) }}"
                                    >
                                        <img
                                            src="{{ asset('assets/images/products/' . $imgPath) }}"
                                            alt="Фото товару"
                                            class="product-gallery-thumb-image"
                                            onerror="this.style.display='none';"
                                        >
                                    </button>
                                @endforeach
                            </div>

                            <div class="product-gallery-main-card">
                                <button type="button" class="product-gallery-arrow left" id="productPrevImage">‹</button>

                                <img
                                    id="mainProductImage"
                                    src="{{ asset('assets/images/products/' . $mainImage) }}"
                                    alt="{{ $product['name'] }}"
                                    class="product-gallery-main-image"
                                    data-current-index="0"
                                    onerror="this.style.display='none'; document.getElementById('mainProductFallback').style.display='flex';"
                                >
                                <div
                                    id="mainProductFallback"
                                    class="product-gallery-main-fallback"
                                    style="display:none;"
                                >
                                    {{ $product['name'] }}
                                </div>

                                <button type="button" class="product-gallery-arrow right" id="productNextImage">›</button>
                            </div>
                        @else
                            <div class="product-gallery-main-card only-main">
                                <div class="product-gallery-main-fallback visible">
                                    {{ $product['name'] }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="product-buy-panel">
                    <div class="product-buy-card">
                        <div class="product-buy-topline">
                            <span class="product-article">Код: {{ $product['id'] }}</span>
                            <span class="product-status {{ (int)$product['stock'] > 0 ? 'in-stock' : 'out-of-stock' }}">
                                {{ (int)$product['stock'] > 0 ? 'Є в наявності' : 'Немає в наявності' }}
                            </span>
                        </div>

                        <h1 class="product-market-title">{{ $product['name'] }}</h1>

                        <div class="product-price-box">
                            <div class="product-price-main">
                                {{ number_format((float)$product['price'], 0, '.', ' ') }} грн
                            </div>
                            <div class="product-price-note">Ціна для онлайн-замовлення</div>
                        </div>

                        <form action="{{ url('/cart/add') }}" method="POST" id="productCartForm">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                            <input type="hidden" name="selected_size" id="selectedSizeInput" value="{{ $sizes[0]['size_label'] ?? '' }}">
                            <input type="hidden" name="selected_color_name" id="selectedColorNameInput" value="{{ $colors[0]['color_name'] ?? '' }}">
                            <input type="hidden" name="selected_color_hex" id="selectedColorHexInput" value="{{ $colors[0]['color_hex'] ?? '' }}">

                            @if (!empty($sizes))
                                <div class="product-size-box">
                                    <div class="product-size-title">Розмір</div>
                                    <div class="product-size-list">
                                        @foreach ($sizes as $index => $size)
                                            <button
                                                type="button"
                                                class="product-size-btn {{ $index === 0 ? 'active' : '' }}"
                                                data-size="{{ $size['size_label'] }}"
                                            >
                                                {{ $size['size_label'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if (!empty($colors))
                                <div class="product-color-box">
                                    <div class="product-size-title">Колір</div>
                                    <div class="product-color-list">
                                        @foreach ($colors as $index => $color)
                                            @php
                                            $buttonColor = '#d9d9df';
                                            if (!empty($color['color_hex'])) {
                                                $buttonColor = strpos($color['color_hex'], '#') === 0
                                                    ? $color['color_hex']
                                                    : ('#' . $color['color_hex']);
                                            }
                                        @endphp
                                        <button
                                            type="button"
                                            class="product-color-btn {{ $index === 0 ? 'active' : '' }}"
                                            data-color-name="{{ $color['color_name'] }}"
                                            data-color-hex="{{ $color['color_hex'] ?? '' }}"
                                            title="{{ $color['color_name'] }}"
                                        >
                                            <span
                                                class="product-color-dot"
                                                data-color="{{ $buttonColor }}"
                                            ></span>
                                            <span class="product-color-name">{{ $color['color_name'] }}</span>
                                        </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="product-main-actions">
                                <button type="submit" class="btn btn-dark product-buy-btn" {{ (int)$product['stock'] == 0 ? 'disabled' : '' }}>
                                    Додати в кошик
                                </button>
                        </form>

                                <form action="{{ url('/favorites/add') }}" method="POST" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                    <button type="submit" class="btn btn-light product-favorite-btn">В обране</button>
                                </form>
                            </div>

                        <div class="product-service-cards">
                            <div class="product-service-card">
                                <h3>Доставка</h3>
                                <p>Самовивіз, кур'єр або пошта. Точні умови додамо пізніше.</p>
                            </div>

                            <div class="product-service-card">
                                <h3>Оплата</h3>
                                <p>Карткою онлайн, при отриманні, або інші методи оплати.</p>
                            </div>

                            <div class="product-service-card">
                                <h3>Гарантія та повернення</h3>
                                <p>Повернення товару протягом 14–30 днів залежно від категорії.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="product-tabs-wrap">
                <div class="product-tabs-nav">
                    <button type="button" class="product-tab-btn active" data-tab="description">Опис</button>
                    <button type="button" class="product-tab-btn" data-tab="characteristics">Характеристики</button>
                    <button type="button" class="product-tab-btn" data-tab="reviews">Відгуки</button>
                </div>

                <div class="product-tab-panels">
                    <section class="product-tab-panel active" data-panel="description">
                        <div class="product-tab-card">
                            <h2>Опис</h2>
                            <p>{!! nl2br(e($product['description'] ?? 'Опис товару буде додано пізніше.')) !!}</p>
                        </div>
                    </section>

                    <section class="product-tab-panel" data-panel="characteristics">
                        <div class="product-tab-card">
                            <h2>Характеристики</h2>
                            <div class="product-specs-table">
                                @foreach ($characteristics as $label => $value)
                                    <div class="product-spec-row">
                                        <div class="product-spec-label">{{ $label }}</div>
                                        <div class="product-spec-value">{{ $value }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    <section class="product-tab-panel" data-panel="reviews">
                        <div class="product-tab-card">
                            <h2>Відгуки та питання</h2>
                            <div class="product-reviews-empty">
                                Поки що відгуків немає. Цей блок уже готовий під майбутню систему коментарів.
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    // Apply color from data attribute to style
    document.querySelectorAll('.product-color-dot[data-color]').forEach(element => {
        element.style.background = element.dataset.color;
    });
</script>
@endsection