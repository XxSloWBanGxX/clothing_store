@php
    $folders = $folders ?? array_keys(session('favorite_folders', ['Обране' => []]));
    if (empty($folders)) {
        $folders = ['Обране'];
    }
    $variant = $variant ?? 'catalog';
@endphp
<div class="fav-picker fav-picker--{{ $variant }}" data-fav-picker>
    @if ($variant === 'product')
        <button type="button" class="btn btn-light pd-fav-btn fav-picker-trigger" aria-expanded="false" aria-haspopup="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
            Додати в обране
        </button>
    @else
        <button type="button" class="catalog-quick-btn fav-picker-trigger" title="Додати в обране" aria-expanded="false" aria-haspopup="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
        </button>
    @endif
    <div class="fav-picker-menu" hidden>
        <p class="fav-picker-title">Додати в список</p>
        @foreach ($folders as $folder)
            <form action="{{ url('/favorites/add') }}" method="POST" class="fav-picker-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ (int) $productId }}">
                <input type="hidden" name="folder" value="{{ $folder }}">
                <button type="submit" class="fav-picker-option">{{ $folder }}</button>
            </form>
        @endforeach
    </div>
</div>
