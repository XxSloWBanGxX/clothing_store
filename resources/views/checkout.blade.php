@extends('layouts.app')

@section('title', 'Оформлення замовлення - CLOTHSTORE')

@section('content')

<main class="checkout-page">
    <section class="catalog-hero">
        <div class="container">
            <div class="catalog-hero-box">
                <span class="hero-badge">CHECKOUT</span>
                <h1>Оформлення замовлення</h1>
                <p>Заповни дані для доставки та обери спосіб оплати.</p>
            </div>
        </div>
    </section>

    <section class="checkout-section">
        <div class="container">
            <form action="{{ url('/checkout') }}" method="POST" class="checkout-layout">
                @csrf

                <div class="checkout-form-col">
                    <div class="profile-card">
                        <h2>Контактні дані</h2>

                        <div class="admin-form-grid">
                            <div class="form-group">
                                <label for="full_name">Імʼя та прізвище</label>
                                <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $user->name) }}">
                                @error('full_name')<small class="form-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="form-group">
                                <label for="phone">Телефон</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')<small class="form-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
                                @error('email')<small class="form-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="form-group">
                                <label for="city">Місто</label>
                                <input type="text" id="city" name="city" value="{{ old('city') }}">
                                @error('city')<small class="form-error">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address_line">Адреса / відділення пошти</label>
                            <input type="text" id="address_line" name="address_line" value="{{ old('address_line') }}">
                            @error('address_line')<small class="form-error">{{ $message }}</small>@enderror
                        </div>
                    </div>

                    <div class="profile-card">
                        <h2>Доставка та оплата</h2>

                        <div class="admin-form-grid">
                            <div class="form-group">
                                <label for="delivery_method">Спосіб доставки</label>
                                <select id="delivery_method" name="delivery_method">
                                    <option value="nova_poshta" {{ old('delivery_method') === 'nova_poshta' ? 'selected' : '' }}>Нова Пошта</option>
                                    <option value="courier" {{ old('delivery_method') === 'courier' ? 'selected' : '' }}>Курʼєр</option>
                                    <option value="pickup" {{ old('delivery_method') === 'pickup' ? 'selected' : '' }}>Самовивіз</option>
                                </select>
                                @error('delivery_method')<small class="form-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="form-group">
                                <label for="payment_method">Спосіб оплати</label>
                                <select id="payment_method" name="payment_method">
                                    <option value="cash_on_delivery" {{ old('payment_method') === 'cash_on_delivery' ? 'selected' : '' }}>Оплата при отриманні</option>
                                    <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>Картка онлайн</option>
                                </select>
                                @error('payment_method')<small class="form-error">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="comment">Коментар до замовлення</label>
                            <textarea id="comment" name="comment" rows="4">{{ old('comment') }}</textarea>
                        </div>
                    </div>
                </div>

                <aside class="checkout-summary">
                    <h2>Твоє замовлення</h2>

                    <div class="checkout-items">
                        @foreach ($cartItems as $item)
                            <div class="checkout-item">
                                <span>{{ $item['name'] }} × {{ (int) $item['quantity'] }}</span>
                                <strong>{{ number_format((float) $item['subtotal'], 0, '.', ' ') }} грн</strong>
                            </div>
                        @endforeach
                    </div>

                    <div class="cart-summary-total">
                        <span>Разом</span>
                        <strong>{{ number_format((float) $total, 0, '.', ' ') }} грн</strong>
                    </div>

                    <button type="submit" class="btn btn-dark" style="width:100%;">Підтвердити замовлення</button>
                    <a href="{{ url('/cart') }}" class="btn btn-light" style="width:100%; margin-top:10px;">Назад до кошика</a>
                </aside>
            </form>
        </div>
    </section>
</main>

@endsection
