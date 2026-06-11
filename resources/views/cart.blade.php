@extends('layouts.app')

@section('content')
<main class="container" style="padding: 60px 0;">
    <h1>Ваш кошик</h1>
    
    @if(empty($cartItems))
        <p>Кошик порожній.</p>
    @else
        <div class="product-market-layout" style="display: flex; gap: 40px;">
            <div style="flex: 2;">
                @foreach($cartItems as $item)
                    <div style="display: flex; align-items: center; padding: 20px; border-bottom: 1px solid #eee;">
                        <img src="{{ asset('assets/images/products/' . $item['image']) }}" width="80" alt="{{ $item['name'] }}">
                        <div style="margin-left: 20px;">
                            <h3>{{ $item['name'] }}</h3>
                            <p>{{ $item['price'] }} грн x {{ $item['quantity'] }} = {{ $item['subtotal'] }} грн</p>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div style="flex: 1; padding: 30px; background: #f9fafb; border-radius: 24px;">
                <h3>Разом: {{ $total }} грн</h3>
                <button class="btn btn-dark" style="width: 100%;">Оформити замовлення</button>
            </div>
        </div>
    @endif
</main>
@endsection