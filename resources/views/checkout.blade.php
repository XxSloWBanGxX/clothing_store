@extends('layouts.app')

@section('title', 'Оформлення замовлення - CLOTHSTORE')

@section('content')

@php
    $paymentOld = old('payment_method', 'card');
@endphp

<main class="checkout-page">
    <section class="catalog-hero catalog-hero-compact">
        <div class="container">
            <div class="catalog-hero-box">
                <span class="hero-badge">CHECKOUT</span>
                <h1>Оформлення замовлення</h1>
                <p>Заповни дані для доставки та оплати — це займе кілька хвилин.</p>
            </div>
        </div>
    </section>

    <section class="checkout-section">
        <div class="container">
            <nav class="checkout-steps" aria-label="Кроки оформлення">
                <a href="{{ url('/cart') }}" class="checkout-step is-done">
                    <span class="checkout-step-num">1</span>
                    <span class="checkout-step-label">Кошик</span>
                </a>
                <span class="checkout-step is-active">
                    <span class="checkout-step-num">2</span>
                    <span class="checkout-step-label">Оформлення</span>
                </span>
                <span class="checkout-step">
                    <span class="checkout-step-num">3</span>
                    <span class="checkout-step-label">Готово</span>
                </span>
            </nav>

            @if ($errors->any())
                <div class="checkout-alert checkout-alert-error">
                    Перевір форму — деякі поля заповнені некоректно.
                </div>
            @endif

            <form action="{{ url('/checkout') }}" method="POST" class="checkout-layout" id="checkoutForm">
                @csrf

                <div class="checkout-main">
                    <section class="checkout-card">
                        <header class="checkout-card-head">
                            <span class="checkout-card-num">1</span>
                            <div>
                                <h2>Контактні дані</h2>
                                <p>Для звʼязку щодо замовлення та доставки</p>
                            </div>
                        </header>

                        <div class="checkout-grid">
                            <div class="form-group checkout-grid-full">
                                <label for="full_name">Імʼя та прізвище</label>
                                <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $user->name) }}" placeholder="Іван Петренко">
                                @error('full_name')<small class="form-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="form-group">
                                <label for="phone">Телефон</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+380 XX XXX XX XX">
                                @error('phone')<small class="form-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="you@email.com">
                                @error('email')<small class="form-error">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </section>

                    <section class="checkout-card">
                        <header class="checkout-card-head">
                            <span class="checkout-card-num">2</span>
                            <div>
                                <h2>Доставка</h2>
                                <p>Адреса з профілю підставиться автоматично</p>
                            </div>
                        </header>

                        @include('partials.delivery-picker', [
                            'prefix' => 'checkout',
                            'saved' => $deliverySaved,
                        ])

                        @error('city')<small class="form-error">{{ $message }}</small>@enderror
                        @error('address_line')<small class="form-error">{{ $message }}</small>@enderror
                        @error('delivery_carrier')<small class="form-error">{{ $message }}</small>@enderror
                    </section>

                    <section class="checkout-card">
                        <header class="checkout-card-head">
                            <span class="checkout-card-num">3</span>
                            <div>
                                <h2>Оплата</h2>
                                <p>Обери зручний спосіб оплати</p>
                            </div>
                        </header>

                        <div class="checkout-pay-options">
                            <label class="checkout-pay-option {{ $paymentOld === 'card' ? 'is-active' : '' }}">
                                <input type="radio" name="payment_method" value="card" {{ $paymentOld === 'card' ? 'checked' : '' }}>
                                <span class="checkout-pay-option-body">
                                    <strong>Картка онлайн</strong>
                                    <small>Оплата одразу після підтвердження</small>
                                </span>
                            </label>

                            <label class="checkout-pay-option {{ $paymentOld === 'cash_on_delivery' ? 'is-active' : '' }}">
                                <input type="radio" name="payment_method" value="cash_on_delivery" {{ $paymentOld === 'cash_on_delivery' ? 'checked' : '' }}>
                                <span class="checkout-pay-option-body">
                                    <strong>Оплата при отриманні</strong>
                                    <small>Готівкою або карткою у відділенні</small>
                                </span>
                            </label>
                        </div>
                        @error('payment_method')<small class="form-error">{{ $message }}</small>@enderror

                        <div class="card-payment-box" id="cardPaymentBox">
                            <div class="card-payment-layout">
                                <div class="card-preview">
                                    <span class="card-preview-chip"></span>
                                    <span class="card-preview-number" id="cardPreviewNumber">•••• •••• •••• ••••</span>
                                    <div class="card-preview-bottom">
                                        <span id="cardPreviewHolder">ІМʼЯ ВЛАСНИКА</span>
                                        <span id="cardPreviewExpiry">MM/YY</span>
                                    </div>
                                </div>

                                <div class="card-payment-fields">
                                    <div class="form-group checkout-grid-full">
                                        <label for="card_number">Номер картки</label>
                                        <input type="text" id="card_number" name="card_number" inputmode="numeric" autocomplete="cc-number"
                                               placeholder="4242 4242 4242 4242" value="{{ old('card_number') }}" maxlength="19">
                                        @error('card_number')<small class="form-error">{{ $message }}</small>@enderror
                                    </div>

                                    <div class="checkout-grid">
                                        <div class="form-group">
                                            <label for="card_expiry">Термін дії</label>
                                            <input type="text" id="card_expiry" name="card_expiry" inputmode="numeric" autocomplete="cc-exp"
                                                   placeholder="MM/YY" value="{{ old('card_expiry') }}" maxlength="5">
                                            @error('card_expiry')<small class="form-error">{{ $message }}</small>@enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="card_cvv">CVV</label>
                                            <input type="password" id="card_cvv" name="card_cvv" inputmode="numeric" autocomplete="cc-csc"
                                                   placeholder="123" value="{{ old('card_cvv') }}" maxlength="4">
                                            @error('card_cvv')<small class="form-error">{{ $message }}</small>@enderror
                                        </div>
                                    </div>

                                    <div class="form-group checkout-grid-full">
                                        <label for="card_holder">Імʼя на картці</label>
                                        <input type="text" id="card_holder" name="card_holder" autocomplete="cc-name"
                                               placeholder="BOGDAN MOSEYCHUK" value="{{ old('card_holder', strtoupper($user->name ?? '')) }}">
                                        @error('card_holder')<small class="form-error">{{ $message }}</small>@enderror
                                    </div>

                                    <p class="card-payment-hint">Демо-картка: <strong>4242 4242 4242 4242</strong>, термін у майбутньому, CVV будь-який.</p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group checkout-comment">
                            <label for="comment">Коментар до замовлення <span class="label-muted">(необовʼязково)</span></label>
                            <textarea id="comment" name="comment" rows="3" placeholder="Наприклад: передзвоніть перед відправкою">{{ old('comment') }}</textarea>
                        </div>
                    </section>
                </div>

                <aside class="checkout-summary">
                    <h2>Твоє замовлення</h2>
                    <p class="checkout-summary-count">{{ count($cartItems) }} {{ count($cartItems) === 1 ? 'товар' : 'товари' }}</p>

                    <div class="checkout-items">
                        @foreach ($cartItems as $item)
                            <article class="checkout-product">
                                <div class="checkout-product-image">
                                    @if (! empty($item['image']))
                                        <img src="{{ asset('assets/images/products/' . $item['image']) }}" alt="{{ $item['name'] }}"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="checkout-product-placeholder" style="display:none;">{{ mb_substr($item['name'], 0, 1) }}</div>
                                    @else
                                        <div class="checkout-product-placeholder">{{ mb_substr($item['name'], 0, 1) }}</div>
                                    @endif
                                </div>

                                <div class="checkout-product-info">
                                    <h3>{{ $item['name'] }}</h3>
                                    @if (! empty($item['selected_size']) || ! empty($item['selected_color_name']))
                                        <p class="checkout-product-meta">
                                            @if (! empty($item['selected_size'])) {{ $item['selected_size'] }} @endif
                                            @if (! empty($item['selected_color_name'])) · {{ $item['selected_color_name'] }} @endif
                                        </p>
                                    @endif
                                    <p class="checkout-product-qty">{{ (int) $item['quantity'] }} × {{ number_format((float) $item['price'], 0, '.', ' ') }} грн</p>
                                </div>

                                <strong class="checkout-product-price">{{ number_format((float) $item['subtotal'], 0, '.', ' ') }} грн</strong>
                            </article>
                        @endforeach
                    </div>

                    <div class="checkout-summary-lines">
                        <div class="checkout-summary-line">
                            <span>Товари</span>
                            <strong>{{ number_format((float) $total, 0, '.', ' ') }} грн</strong>
                        </div>
                        <div class="checkout-summary-line">
                            <span>Доставка</span>
                            <strong>За тарифами перевізника</strong>
                        </div>
                    </div>

                    <div class="checkout-summary-total">
                        <span>До сплати</span>
                        <strong id="checkoutTotal">{{ number_format((float) $total, 0, '.', ' ') }} грн</strong>
                    </div>

                    <button type="submit" class="btn btn-dark checkout-submit" id="checkoutSubmitBtn">Оплатити та підтвердити</button>
                    <a href="{{ url('/cart') }}" class="btn btn-light checkout-back">← Назад до кошика</a>

                    <p class="checkout-summary-note">Натискаючи кнопку, ти погоджуєшся з умовами оформлення замовлення.</p>
                </aside>
            </form>
        </div>
    </section>
</main>

<script src="{{ asset('assets/js/checkout.js') }}"></script>
<script src="{{ asset('assets/js/delivery-picker.js') }}"></script>

@endsection
