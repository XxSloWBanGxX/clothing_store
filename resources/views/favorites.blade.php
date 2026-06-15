@extends('layouts.app')

@section('title', 'Обране - CLOTHSTORE')

@section('content')

<main class="favorites-page">
    <section class="catalog-hero">
        <div class="container">
            <div class="catalog-hero-box">
                <span class="hero-badge">FAVORITES</span>
                <h1>Списки бажань</h1>
                <p>Створюй папки та розкладай товари так, як тобі зручно.</p>
            </div>
        </div>
    </section>

    <section class="favorites-section">
        <div class="container">
            @if (session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            <div class="wishlist-header-bar">
                <form action="{{ url('/favorites/create-folder') }}" method="POST" class="wishlist-create-form">
                    @csrf
                    <input type="text" name="folder_name" placeholder="Нова папка..." required>
                    <button type="submit" class="wishlist-add-btn">+</button>
                </form>
            </div>

            <div class="wishlist-folder-list">
                @foreach ($data['foldersData'] as $folderName => $items)
                    <div class="wishlist-folder-card">
                        <div class="wishlist-folder-top">
                            <div>
                                <h2>
                                    {{ $folderName }}
                                    @if ($folderName === 'Обране')
                                        <span class="wishlist-main-badge">(Основний)</span>
                                    @endif
                                </h2>
                                <p>Кількість товарів: {{ count($items) }}</p>
                            </div>

                            <div class="wishlist-folder-controls">
                                <form action="{{ url('/favorites/clear-folder') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="folder" value="{{ $folderName }}">
                                    <button type="submit" class="wishlist-icon-btn">↻</button>
                                </form>

                                @if ($folderName !== 'Обране')
                                    <form action="{{ url('/favorites/delete-folder') }}" method="POST" onsubmit="return confirm('Видалити папку?');">
                                        @csrf
                                        <input type="hidden" name="folder" value="{{ $folderName }}">
                                        <button type="submit" class="wishlist-icon-btn">⋮</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        @if (! empty($items))
                            <div class="wishlist-preview-row">
                                @foreach (array_slice($items, 0, 6) as $product)
                                    <a href="{{ url('/product/' . (int) $product['id']) }}" class="wishlist-preview-item">
                                        @if (! empty($product['image']))
                                            <img
                                                src="{{ asset('assets/images/products/' . $product['image']) }}"
                                                alt="{{ $product['name'] }}"
                                                class="wishlist-preview-image"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                            >
                                            <div class="wishlist-preview-fallback" style="display:none;">
                                                {{ $product['name'] }}
                                            </div>
                                        @else
                                            <div class="wishlist-preview-fallback">
                                                {{ $product['name'] }}
                                            </div>
                                        @endif
                                    </a>
                                @endforeach
                            </div>

                            <div class="wishlist-products-grid">
                                @foreach ($items as $product)
                                    <div class="wishlist-product-mini">
                                        <a href="{{ url('/product/' . (int) $product['id']) }}" class="wishlist-mini-title">
                                            {{ $product['name'] }}
                                        </a>

                                        <div class="wishlist-mini-actions">
                                            <form action="{{ url('/cart/add') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ (int) $product['id'] }}">
                                                <button type="submit" class="btn btn-dark btn-sm">В кошик</button>
                                            </form>

                                            <form action="{{ url('/favorites/remove') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ (int) $product['id'] }}">
                                                <input type="hidden" name="folder" value="{{ $folderName }}">
                                                <button type="submit" class="btn btn-light btn-sm">Видалити</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="wishlist-empty-line">Список бажань порожній</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</main>

@endsection
