@php
    $onSale = ! empty($product['on_sale'])
        || (! empty($product['old_price']) && (float) $product['old_price'] > (float) $product['price']);
    $discountPercent = (int) ($product['discount_percent'] ?? 0);
    if ($onSale && $discountPercent === 0 && ! empty($product['old_price'])) {
        $discountPercent = (int) round((1 - (float) $product['price'] / (float) $product['old_price']) * 100);
    }
@endphp

@if ($onSale)
    <p class="product-price {{ $class ?? '' }}">
        <span class="price-sale">{{ number_format((float) $product['price'], 0, '.', ' ') }} грн</span>
        <span class="price-old">{{ number_format((float) $product['old_price'], 0, '.', ' ') }} грн</span>
        @if ($showBadge ?? false)
            <span class="sale-badge">-{{ $discountPercent }}%</span>
        @endif
    </p>
@else
    <p class="product-price {{ $class ?? '' }}">
        {{ number_format((float) $product['price'], 0, '.', ' ') }} грн
    </p>
@endif
