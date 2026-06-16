@extends('layouts.app')

@section('title', 'Знижки — CLOTHSTORE')

@section('content')
@php
    $products = $data['products'] ?? [];
    $activeSales = $data['activeSales'] ?? collect();
    $scheduledSales = $data['scheduledSales'] ?? collect();
    $serverNow = $data['serverNow'] ?? now()->toIso8601String();
@endphp

<main class="page-main">
    <section class="sale-hero">
        <div class="container">
            <span class="section-label">SALE</span>
            <h1 class="page-title">Знижки та акції</h1>
            <p class="page-lead">Актуальні пропозиції оновлюються автоматично за серверним часом ({{ config('app.timezone') }})</p>

            @if ($activeSales->isNotEmpty())
                <div class="sale-campaigns">
                    @foreach ($activeSales as $sale)
                        <article class="sale-campaign-card" @if($sale->ends_at) data-ends-at="{{ \Illuminate\Support\Carbon::parse($sale->ends_at)->toIso8601String() }}" @endif>
                            <div class="sale-campaign-head">
                                <h2>{{ $sale->title }}</h2>
                                <span class="sale-campaign-badge">-{{ (int) $sale->discount_percent }}%</span>
                            </div>
                            @if ($sale->description)
                                <p>{{ $sale->description }}</p>
                            @endif
                            <div class="sale-campaign-meta">
                                @if ($sale->ends_at)
                                    <span class="sale-countdown" data-ends-at="{{ \Illuminate\Support\Carbon::parse($sale->ends_at)->toIso8601String() }}">
                                        Залишилось: <strong>—</strong>
                                    </span>
                                @else
                                    <span>Без терміну дії</span>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            @if ($scheduledSales->isNotEmpty())
                <div class="sale-scheduled">
                    <h3>Незабаром</h3>
                    <ul>
                        @foreach ($scheduledSales as $sale)
                            <li>
                                <strong>{{ $sale->title }}</strong>
                                — {{ (int) $sale->discount_percent }}%,
                                з {{ \Illuminate\Support\Carbon::parse($sale->starts_at)->format('d.m.Y H:i') }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </section>

    <section class="catalog-section">
        <div class="container">
            <div class="catalog-head">
                <h2>Товари зі знижкою</h2>
                <p>{{ count($products) }} позицій</p>
            </div>

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
                                        <span class="catalog-item-badge sale">-{{ (int) ($product['discount_percent'] ?? 0) }}%</span>
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
                                    @include('partials.product-price', ['product' => $product, 'class' => 'catalog-item-price'])
                                    <span class="catalog-item-stock {{ $inStock ? 'in' : 'out' }}">
                                        {{ $inStock ? 'В наявності' : 'Немає' }}
                                    </span>
                                </div>

                                <a href="{{ url('/product/' . $product['id']) }}" class="catalog-item-more">Детальніше →</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="catalog-empty">
                    <div class="catalog-empty-icon">🏷</div>
                    <h3>Зараз немає активних знижок</h3>
                    <p>Слідкуй за розділом — акції з’являться автоматично за розкладом.</p>
                    <a href="{{ url('/catalog') }}" class="btn btn-dark">Перейти в каталог</a>
                </div>
            @endif
        </div>
    </section>
</main>

<script>
(function () {
    function formatCountdown(ms) {
        if (ms <= 0) return 'Завершено';
        const d = Math.floor(ms / 86400000);
        const h = Math.floor((ms % 86400000) / 3600000);
        const m = Math.floor((ms % 3600000) / 60000);
        const s = Math.floor((ms % 60000) / 1000);
        const parts = [];
        if (d) parts.push(d + ' д');
        parts.push(String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0'));
        return parts.join(' ');
    }

    function tick() {
        const now = Date.now();
        document.querySelectorAll('.sale-countdown[data-ends-at]').forEach(el => {
            const end = new Date(el.dataset.endsAt).getTime();
            const strong = el.querySelector('strong');
            if (strong) strong.textContent = formatCountdown(end - now);
        });
    }

    tick();
    setInterval(tick, 1000);
})();
</script>
@endsection
