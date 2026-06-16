@php
    $products = $products ?? [];
    $title = $title ?? 'Нещодавно переглянуті';
    $label = $label ?? 'RECENT';
@endphp

@if (! empty($products))
    <section class="products-section rv-section">
        <div class="container">
            <div class="section-head">
                <div>
                    <span class="section-label">{{ $label }}</span>
                    <h2>{{ $title }}</h2>
                </div>
            </div>

            <div class="products-grid">
                @foreach ($products as $product)
                    <div class="product-card">
                        <div class="product-image">
                            @if (! empty($product['category_name']))
                                <span class="product-tag">{{ $product['category_name'] }}</span>
                            @endif
                            @if (! empty($product['image']))
                                <img src="{{ asset('assets/images/products/' . $product['image']) }}" alt="{{ $product['name'] }}" class="home-product-image" loading="lazy">
                            @else
                                <div class="image-placeholder">{{ $product['name'] }}</div>
                            @endif
                        </div>
                        <div class="product-info">
                            <h3>{{ $product['name'] }}</h3>
                            @include('partials.product-price', ['product' => $product])
                            <a href="{{ url('/product/' . $product['id']) }}" class="btn btn-small">Детальніше</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
