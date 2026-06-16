@extends('layouts.app')

@section('title', 'Спільний список — CLOTHSTORE')

@section('content')
<main class="favorites-page favorites-page-v2">
    <section class="fav-top">
        <div class="container">
            <nav class="fav-breadcrumbs" aria-label="Навігація">
                <a href="{{ url('/') }}">Головна</a>
                <span>/</span>
                <a href="{{ url('/favorites') }}">Обране</a>
                <span>/</span>
                <span>{{ $share->folder_name }}</span>
            </nav>

            <div class="fav-top-copy">
                <span class="section-label">SHARED WISHLIST</span>
                <h1>{{ $share->folder_name }}</h1>
                <p>Спільний список бажань — {{ count($items) }} {{ count($items) === 1 ? 'товар' : 'товари' }}</p>
            </div>
        </div>
    </section>

    <section class="fav-section">
        <div class="container">
            <div class="fav-share-actions">
                <form action="{{ url('/favorites/share/' . $share->token . '/import') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-dark">Додати до мого обраного</button>
                </form>
                <a href="{{ url('/catalog') }}" class="btn btn-light">Перейти в каталог</a>
            </div>

            @if (! empty($items))
                <div class="catalog-grid-v2 fav-products-grid">
                    @foreach ($items as $product)
                        @php
                            $onSale = ! empty($product['old_price']) && (float) $product['old_price'] > (float) $product['price'];
                            $inStock = (int) ($product['stock'] ?? 0) > 0;
                        @endphp
                        <article class="catalog-item fav-item">
                            <div class="catalog-item-media">
                                <a href="{{ url('/product/' . (int) $product['id']) }}" class="catalog-item-link">
                                    @if ($onSale)
                                        <span class="catalog-item-badge sale">Sale</span>
                                    @endif
                                    @if (! empty($product['image']))
                                        <img src="{{ asset('assets/images/products/' . $product['image']) }}" alt="{{ $product['name'] }}" class="catalog-item-image" loading="lazy">
                                    @else
                                        <div class="catalog-item-fallback">{{ $product['name'] }}</div>
                                    @endif
                                </a>
                            </div>
                            <div class="catalog-item-body">
                                <h3 class="catalog-item-title">
                                    <a href="{{ url('/product/' . (int) $product['id']) }}">{{ $product['name'] }}</a>
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
                                    <span class="catalog-item-stock {{ $inStock ? 'in' : 'out' }}">{{ $inStock ? 'В наявності' : 'Немає' }}</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="fav-empty">
                    <h3>Список порожній</h3>
                    <p>Товари з цього списку більше недоступні.</p>
                </div>
            @endif
        </div>
    </section>
</main>
@endsection
