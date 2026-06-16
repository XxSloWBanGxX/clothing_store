@extends('admin.layout')

@section('title', 'Відгуки')

@section('admin_content')
@include('partials.admin-flash')

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Відгуки клієнтів</h2>
            <p>{{ $reviews->count() }} відгуків@if(($pendingCount ?? 0) > 0) · {{ $pendingCount }} на модерації@endif</p>
        </div>
        <div class="adm-filter-tabs">
            <a href="{{ url('/admin/reviews') }}" class="adm-filter-tab {{ ($statusFilter ?? '') === '' ? 'is-active' : '' }}">Усі</a>
            <a href="{{ url('/admin/reviews?status=pending') }}" class="adm-filter-tab {{ ($statusFilter ?? '') === 'pending' ? 'is-active' : '' }}">На модерації</a>
            <a href="{{ url('/admin/reviews?status=approved') }}" class="adm-filter-tab {{ ($statusFilter ?? '') === 'approved' ? 'is-active' : '' }}">Опубліковані</a>
        </div>
    </div>

    <div class="adm-review-list">
        @forelse ($reviews as $review)
            <article class="adm-review-card">
                <div class="adm-review-head">
                    <div>
                        <strong>{{ $review->author_name }}</strong>
                        <span class="adm-cell-muted">{{ $review->created_at }}</span>
                        @if (isset($review->is_approved) && ! $review->is_approved)
                            <span class="adm-badge adm-badge--warn">Очікує модерації</span>
                        @endif
                    </div>
                    <div class="adm-review-rating" aria-label="Оцінка {{ (int) $review->rating }} з 5">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= (int) $review->rating ? 'is-on' : '' }}">★</span>
                        @endfor
                    </div>
                </div>
                <a href="{{ url('/product/' . $review->product_id) }}" class="adm-review-product" target="_blank">
                    {{ $review->product_name }}
                </a>
                <p class="adm-review-text">{{ $review->comment }}</p>
                <div class="adm-row-actions">
                    @if (isset($review->is_approved) && ! $review->is_approved)
                        <form action="{{ url('/admin/reviews/' . $review->id . '/approve') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-dark btn-sm">Опублікувати</button>
                        </form>
                    @endif
                    <form action="{{ url('/admin/reviews/' . $review->id) }}" method="POST" onsubmit="return confirm('Видалити відгук?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-light btn-sm">Видалити</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="adm-empty">
                <h3>Відгуків поки немає</h3>
                <p>Коли клієнти залишать відгуки на товари, вони зʼявляться тут.</p>
            </div>
        @endforelse
    </div>
</section>
@endsection
