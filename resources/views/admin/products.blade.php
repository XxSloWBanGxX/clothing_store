@extends('admin.layout')

@section('title', 'Товари')

@section('admin_content')
@include('partials.admin-flash')

<section class="adm-panel adm-panel--catalog">
    <div class="adm-catalog-head">
        <div class="adm-catalog-head-top">
            <div>
                <h2>Каталог товарів</h2>
                <p>{{ $products->count() }} позицій</p>
            </div>
            <div class="adm-panel-actions">
                <a href="{{ url('/admin/products/import') }}" class="btn btn-light btn-sm">⇪ Імпорт CSV</a>
                <a href="{{ url('/admin/products/create') }}" class="btn btn-dark btn-sm">+ Додати товар</a>
            </div>
        </div>

        <form action="{{ url('/admin/products') }}" method="GET" class="adm-toolbar adm-toolbar--catalog">
            <div class="adm-search">
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Пошук за назвою або slug…">
            </div>
            <select name="category" class="adm-select">
                <option value="">Усі категорії</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ (string) ($filters['category'] ?? '') === (string) $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <select name="stock" class="adm-select">
                <option value="">Усі залишки</option>
                <option value="in" {{ ($filters['stock'] ?? '') === 'in' ? 'selected' : '' }}>В наявності</option>
                <option value="out" {{ ($filters['stock'] ?? '') === 'out' ? 'selected' : '' }}>Немає в наявності</option>
                <option value="low" {{ ($filters['stock'] ?? '') === 'low' ? 'selected' : '' }}>Низький залишок</option>
                <option value="sale" {{ ($filters['stock'] ?? '') === 'sale' ? 'selected' : '' }}>Зі знижкою</option>
            </select>
            <div class="adm-toolbar-actions">
                <button type="submit" class="btn btn-dark btn-sm">Фільтрувати</button>
                @if (! empty($filters['q']) || ! empty($filters['category']) || ! empty($filters['stock']))
                    <a href="{{ url('/admin/products') }}" class="btn btn-light btn-sm">Скинути</a>
                @endif
            </div>
        </form>
    </div>

    @if ($products->isNotEmpty())
        <div class="adm-table-wrap">
            <table class="adm-table adm-table--products">
                <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Категорія</th>
                        <th>Ціна</th>
                        <th>Залишок</th>
                        <th>Статус</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        @php
                            $onSale = ! empty($product->old_price) && (float) $product->old_price > (float) $product->price;
                        @endphp
                        <tr>
                            <td>
                                <div class="adm-product-cell">
                                    <div class="adm-product-thumb">
                                        @if (! empty($product->image))
                                            <img src="{{ asset('assets/images/products/' . $product->image) }}" alt="">
                                        @else
                                            <span>{{ mb_substr($product->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <strong>{{ $product->name }}</strong>
                                        <span class="adm-cell-muted">#{{ (int) $product->id }} · {{ $product->slug }}</span>
                                        @if (! empty($product->is_featured))
                                            <span class="adm-badge adm-badge--info adm-badge--inline">Featured</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->category_name }}</td>
                            <td>
                                @if ($onSale)
                                    <strong>{{ number_format((float) $product->price, 0, '.', ' ') }} грн</strong>
                                    <span class="adm-cell-muted adm-price-old">{{ number_format((float) $product->old_price, 0, '.', ' ') }} грн</span>
                                @else
                                    {{ number_format((float) $product->price, 0, '.', ' ') }} грн
                                @endif
                            </td>
                            <td>{{ (int) $product->stock }}</td>
                            <td>
                                @if ((int) $product->stock > 0)
                                    <span class="adm-badge adm-badge--success">В наявності</span>
                                @else
                                    <span class="adm-badge adm-badge--danger">Немає</span>
                                @endif
                            </td>
                            <td>
                                <div class="adm-row-actions">
                                    <a href="{{ url('/product/' . $product->id) }}" class="btn btn-light btn-sm" target="_blank">На сайті</a>
                                    <a href="{{ url('/admin/products/' . $product->id . '/edit') }}" class="btn btn-dark btn-sm">Редагувати</a>
                                    <form action="{{ url('/admin/products/' . $product->id) }}" method="POST" onsubmit="return confirm('Видалити товар?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-sm">Видалити</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="adm-empty">
            <h3>Товарів не знайдено</h3>
            <p>Спробуй змінити фільтри або додай новий товар.</p>
            <a href="{{ url('/admin/products/create') }}" class="btn btn-dark">Додати товар</a>
        </div>
    @endif
</section>
@endsection
