@extends('layouts.app')

@section('title', 'Обране - CLOTHSTORE')

@section('content')
@php
    $foldersData = $data['foldersData'] ?? [];
    $activeFolder = $data['activeFolder'] ?? 'Обране';
    $activeItems = $data['activeItems'] ?? [];
@endphp

<main class="favorites-page favorites-page-v2">
    <section class="fav-top">
        <div class="container">
            <nav class="fav-breadcrumbs" aria-label="Навігація">
                <a href="{{ url('/') }}">Головна</a>
                <span>/</span>
                <span>Обране</span>
            </nav>

            <div class="fav-top-copy">
                <span class="section-label">WISHLIST</span>
                <h1>Списки бажань</h1>
                <p>Обирай папку зліва — товари відображаються праворуч, як у зручному списку покупок.</p>
            </div>
        </div>
    </section>

    <section class="fav-section">
        <div class="container">
            @if (session('success'))
                <div class="alert-success fav-alert">{{ session('success') }}</div>
            @endif

            @if (empty($foldersData))
                <div class="fav-empty">
                    <div class="fav-empty-icon">♡</div>
                    <h3>Списків поки немає</h3>
                    <p>Додай товари в обране з каталогу — вони зʼявляться тут.</p>
                    <a href="{{ url('/catalog') }}" class="btn btn-dark">Перейти в каталог</a>
                </div>
            @else
                <div class="fav-rozetka-layout">
                    <aside class="fav-sidebar" aria-label="Папки обраного">
                        <div class="fav-sidebar-head">
                            <h2>Мої списки</h2>
                        </div>

                        <nav class="fav-folder-nav">
                            @foreach ($foldersData as $folderName => $items)
                                <a href="{{ url('/favorites?folder=' . urlencode($folderName)) }}"
                                   class="fav-folder-link {{ $activeFolder === $folderName ? 'is-active' : '' }}">
                                    <span class="fav-folder-link-icon">♡</span>
                                    <span class="fav-folder-link-name">{{ $folderName }}</span>
                                    <span class="fav-folder-link-count">{{ count($items) }}</span>
                                </a>
                            @endforeach
                        </nav>

                        <form action="{{ url('/favorites/create-folder') }}" method="POST" class="fav-sidebar-create">
                            @csrf
                            <input type="text" name="folder_name" placeholder="Новий список..." required maxlength="40">
                            <button type="submit" class="fav-sidebar-add" aria-label="Створити список">+</button>
                        </form>
                    </aside>

                    <div class="fav-main">
                        <div class="fav-main-head">
                            <div>
                                <h2>{{ $activeFolder }}</h2>
                                @if ($activeFolder === 'Обране')
                                    <span class="fav-folder-badge">Основний список</span>
                                @endif
                            </div>
                            <div class="fav-main-actions">
                                @if (! empty($activeItems))
                                    <form action="{{ url('/favorites/clear-folder') }}" method="POST" onsubmit="return confirm('Очистити список «{{ $activeFolder }}»?');">
                                        @csrf
                                        <input type="hidden" name="folder" value="{{ $activeFolder }}">
                                        <button type="submit" class="btn btn-light btn-sm">Очистити список</button>
                                    </form>
                                @endif
                                @if ($activeFolder !== 'Обране')
                                    <form action="{{ url('/favorites/delete-folder') }}" method="POST" onsubmit="return confirm('Видалити список «{{ $activeFolder }}»?');">
                                        @csrf
                                        <input type="hidden" name="folder" value="{{ $activeFolder }}">
                                        <button type="submit" class="btn btn-light btn-sm fav-delete-btn">Видалити список</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        @if (! empty($activeItems))
                            <div class="catalog-grid-v2 fav-products-grid">
                                @foreach ($activeItems as $product)
                                    @php
                                        $onSale = ! empty($product['old_price']) && (float) $product['old_price'] > (float) $product['price'];
                                        $inStock = (int) ($product['stock'] ?? 0) > 0;
                                    @endphp
                                    <article class="catalog-item fav-item">
                                        <div class="catalog-item-media">
                                            <div class="catalog-item-quick">
                                                @if ($inStock)
                                                    <form action="{{ url('/cart/add') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ (int) $product['id'] }}">
                                                        <button type="submit" class="catalog-quick-btn" title="В кошик">
                                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ url('/favorites/remove') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ (int) $product['id'] }}">
                                                    <input type="hidden" name="folder" value="{{ $activeFolder }}">
                                                    <button type="submit" class="catalog-quick-btn fav-remove-btn" title="Прибрати">
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                                    </button>
                                                </form>
                                            </div>

                                            <a href="{{ url('/product/' . (int) $product['id']) }}" class="catalog-item-link" tabindex="-1" aria-hidden="true">
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
                                            @if (count($foldersData) > 1)
                                                @include('partials.favorite-move-picker', [
                                                    'productId' => (int) $product['id'],
                                                    'fromFolder' => $activeFolder,
                                                    'targetFolders' => array_values(array_filter(
                                                        array_keys($foldersData),
                                                        fn ($name) => $name !== $activeFolder
                                                    )),
                                                ])
                                            @endif
                                            <a href="{{ url('/product/' . (int) $product['id']) }}" class="catalog-item-more">Детальніше →</a>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="fav-folder-empty">
                                <div class="fav-empty-icon">♡</div>
                                <h3>Список порожній</h3>
                                <p>Додай товари в «{{ $activeFolder }}» з каталогу або картки товару.</p>
                                <a href="{{ url('/catalog') }}" class="btn btn-dark">Перейти в каталог</a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>
</main>
@endsection
