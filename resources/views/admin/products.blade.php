@extends('admin.layout')

@section('title', 'Керування товарами')

@section('admin_content')

@if (session('status'))
    <div class="alert-success">{{ session('status') }}</div>
@endif

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Список товарів</h2>
        <a href="{{ url('/admin/products/create') }}" class="btn btn-dark">+ Додати товар</a>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>Категорія</th>
                    <th>Ціна</th>
                    <th>Кількість</th>
                    <th>Статус</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>#{{ (int) $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category_name }}</td>
                        <td>{{ number_format((float) $product->price, 0, '.', ' ') }} грн</td>
                        <td>{{ (int) $product->stock }}</td>
                        <td>
                            @if ((int) $product->stock > 0)
                                <span class="admin-badge success">В наявності</span>
                            @else
                                <span class="admin-badge danger">Немає</span>
                            @endif
                        </td>
                        <td>
                            <div class="admin-actions">
                                <a href="{{ url('/admin/products/' . $product->id . '/edit') }}" class="btn btn-light btn-sm">Редагувати</a>

                                <form action="{{ url('/admin/products/' . $product->id) }}" method="POST" onsubmit="return confirm('Видалити товар?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-dark btn-sm">Видалити</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Поки що товарів немає.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@endsection
