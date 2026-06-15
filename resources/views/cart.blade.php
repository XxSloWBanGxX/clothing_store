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
                                    <p>{{ number_format((float) $item['price'], 0, '.', ' ') }} грн × {{ (int) $item['quantity'] }}</p>
                                    <strong>{{ number_format((float) $item['subtotal'], 0, '.', ' ') }} грн</strong>
                                </div>

                                <form action="{{ url('/cart/remove') }}" method="POST" class="cart-item-remove">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ (int) $item['id'] }}">
                                    <button type="submit" class="btn btn-light btn-sm">Видалити</button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    <aside class="cart-summary">
                        <h3>Разом</h3>
                        <div class="cart-summary-total">
                            <span>До сплати</span>
                            <strong>{{ number_format((float) $total, 0, '.', ' ') }} грн</strong>
                        </div>
                        <a href="{{ url('/catalog') }}" class="btn btn-light" style="width:100%;">Продовжити покупки</a>
                    </aside>
                </div>
            @endif
        </div>
    </section>
</main>

@endsection
