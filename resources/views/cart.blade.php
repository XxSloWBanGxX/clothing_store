@extends('layouts.app')

@section('title', 'Кошик - CLOTHSTORE')

@section('content')

<main class="cart-page">
    <section class="catalog-hero">
        <div class="container">
            <div class="catalog-hero-box">
                <span class="hero-badge">CART</span>
                <h1>Кошик</h1>
                <p>Перевір вибрані товари перед оформленням замовлення.</p>
            </div>
        </div>
    </section>

    <section class="cart-section">
        <div class="container">
            @if (session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            @if (session('stockError'))
                <div class="alert-error">{{ session('stockError') }}</div>
            @endif

            @if (empty($cartItems))
                <div class="empty-box">
                    <h3>Кошик порожній</h3>
                    <p>Додай товари з каталогу, щоб оформити замовлення.</p>
                    <a href="{{ url('/catalog') }}" class="btn btn-dark">Перейти в каталог</a>
                </div>
            @else
                <div class="cart-layout">
                    <div class="cart-items">
                        @foreach ($cartItems as $item)
                            <div class="cart-item">
                                <div class="cart-item-image">
                                    @if (! empty($item['image']))
                                        <img
                                            src="{{ asset('assets/images/products/' . $item['image']) }}"
                                            alt="{{ $item['name'] }}"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        >
                                        <div class="product-image-placeholder" style="display:none;">{{ $item['name'] }}</div>
                                    @else
                                        <div class="product-image-placeholder">{{ $item['name'] }}</div>
                                    @endif
                                </div>

                                <div class="cart-item-info">
                                    <h3>{{ $item['name'] }}</h3>
                                    @if (! empty($item['selected_size']) || ! empty($item['selected_color_name']))
                                        <p class="cart-item-meta">
                                            @if (! empty($item['selected_size'])) Розмір: {{ $item['selected_size'] }} @endif
                                            @if (! empty($item['selected_color_name'])) · Колір: {{ $item['selected_color_name'] }} @endif
                                        </p>
                                    @endif
                                    <p>{{ number_format((float) $item['price'], 0, '.', ' ') }} грн</p>

                                    <div class="cart-qty">
                                        <form action="{{ url('/cart/update') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ (int) $item['id'] }}">
                                            <input type="hidden" name="action" value="decrease">
                                            <button type="submit" class="cart-qty-btn">−</button>
                                        </form>
                                        <span class="cart-qty-value">{{ (int) $item['quantity'] }}</span>
                                        <form action="{{ url('/cart/update') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ (int) $item['id'] }}">
                                            <input type="hidden" name="action" value="increase">
                                            <button type="submit" class="cart-qty-btn">+</button>
                                        </form>
                                    </div>
                                </div>

                                <div class="cart-item-side">
                                    <strong class="cart-item-subtotal">{{ number_format((float) $item['subtotal'], 0, '.', ' ') }} грн</strong>
                                    <form action="{{ url('/cart/remove') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ (int) $item['id'] }}">
                                        <button type="submit" class="btn btn-light btn-sm">Видалити</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <aside class="cart-summary">
                        <h3>Разом</h3>
                        <div class="cart-summary-total">
                            <span>До сплати</span>
                            <strong>{{ number_format((float) $total, 0, '.', ' ') }} грн</strong>
                        </div>

                        @auth
                            <a href="{{ url('/checkout') }}" class="btn btn-dark" style="width:100%;">Оформити замовлення</a>
                        @else
                            <a href="{{ url('/login') }}" class="btn btn-dark" style="width:100%;">Увійти для замовлення</a>
                        @endauth

                        <a href="{{ url('/catalog') }}" class="btn btn-light" style="width:100%; margin-top:10px;">Продовжити покупки</a>
                    </aside>
                </div>
            @endif
        </div>
    </section>
</main>

@endsection
