@extends('layouts.app')

@section('title', ($page->title ?? 'Про нас') . ' - CLOTHSTORE')

@section('content')
@php
    $brand = ($site['brand_lead'] ?? 'CLOTH') . ($site['brand_accent'] ?? 'STORE');
    $story = $about['story'] ?? [];
    $values = $about['values'] ?? ['title' => '', 'text' => '', 'tags' => []];
@endphp

<main class="about-page">
    <section class="about-hero">
        <div class="container">
            <div class="about-hero-box">
                <span class="hero-badge">ABOUT US</span>
                <h1>{{ $page->title ?? 'Про нас' }}</h1>
                @if (! empty($page->subtitle))
                    <p>{{ $page->subtitle }}</p>
                @endif
            </div>
        </div>
    </section>

    <section class="about-story-section">
        <div class="container">
            <div class="about-story-grid">
                <div class="about-story-card">
                    <span class="section-label">НАША ІДЕЯ</span>
                    <h2>{{ $about['story_heading'] ?? '' }}</h2>
                    @forelse ($story as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @empty
                        <p>Ми створюємо простір для стильних покупок — від перегляду каталогу до доставки додому.</p>
                    @endforelse
                    <div class="about-story-actions">
                        <a href="{{ url('/catalog') }}" class="btn btn-dark">Перейти в каталог</a>
                        <a href="{{ url('/new') }}" class="btn btn-light">Новинки</a>
                    </div>
                </div>

                <div class="about-visual-card">
                    <div class="about-visual-box">
                        <div class="about-visual-main">{{ $site['brand_name'] ?? $brand }}</div>
                        <div class="about-visual-tag tag-top">Minimal Style</div>
                        <div class="about-visual-tag tag-bottom">Online Fashion</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-stats-section">
        <div class="container">
            <div class="about-stats-grid">
                <div class="about-stat-item">
                    <h3>{{ $site['hero_stat1_value'] ?? '500+' }}</h3>
                    <p>{{ $site['hero_stat1_label'] ?? 'Товарів у каталозі' }}</p>
                </div>
                <div class="about-stat-item">
                    <h3>{{ $site['hero_stat2_value'] ?? '24/7' }}</h3>
                    <p>{{ $site['hero_stat2_label'] ?? 'Онлайн-замовлення' }}</p>
                </div>
                <div class="about-stat-item">
                    <h3>1–2 дні</h3>
                    <p>Відправка замовлень після підтвердження</p>
                </div>
                <div class="about-stat-item">
                    <h3>14 днів</h3>
                    <p>На повернення без зайвих питань</p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-features-section">
        <div class="container">
            <div class="section-head">
                <div>
                    <span class="section-label">ПЕРЕВАГИ</span>
                    <h2>Чому клієнти обирають {{ $site['brand_name'] ?? 'ClothStore' }}</h2>
                </div>
            </div>

            <div class="about-features-grid">
                <div class="about-feature-card">
                    <span class="about-feature-icon">▦</span>
                    <h3>Зручний каталог</h3>
                    <p>Фільтри, категорії та швидкий перехід до картки товару з фото, розмірами та кольорами.</p>
                </div>
                <div class="about-feature-card">
                    <span class="about-feature-icon">🛒</span>
                    <h3>Особистий кошик</h3>
                    <p>Товари зберігаються для вашого акаунта — можна повернутись і завершити покупку пізніше.</p>
                </div>
                <div class="about-feature-card">
                    <span class="about-feature-icon">♥</span>
                    <h3>Списки обраного</h3>
                    <p>Зберігайте моделі в папках, порівнюйте та повертайтесь до них у зручний момент.</p>
                </div>
                <div class="about-feature-card">
                    <span class="about-feature-icon">🚚</span>
                    <h3>Доставка по Україні</h3>
                    <p>{{ $site['delivery_carriers'] ?? 'Nova Poshta, Ukrposhta, Meest' }} — оберіть зручний спосіб отримання.</p>
                </div>
                <div class="about-feature-card">
                    <span class="about-feature-icon">🏷</span>
                    <h3>Промокоди та бонуси</h3>
                    <p>Акції для нових клієнтів, персональні знижки та бонусна програма лояльності.</p>
                </div>
                <div class="about-feature-card">
                    <span class="about-feature-icon">💬</span>
                    <h3>Підтримка</h3>
                    <p>Звертайтесь через чат на сайті — команда допоможе з замовленням, розміром або доставкою.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-values-section">
        <div class="container">
            <div class="about-values-box">
                <span class="section-label">НАШ ПІДХІД</span>
                <h2>{{ $values['title'] }}</h2>
                <p>{{ $values['text'] }}</p>

                @if (! empty($values['tags']))
                    <div class="about-values-list">
                        @foreach ($values['tags'] as $tag)
                            <div class="about-value-item">{{ $tag }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="about-cta-section">
        <div class="container">
            <div class="about-cta-box">
                <div>
                    <span class="section-label">SHOP NOW</span>
                    <h2>Готові оновити гардероб?</h2>
                    <p>Перегляньте новинки або підпишіться на розсилку — першими дізнавайтесь про знижки та колекції.</p>
                </div>
                <div class="about-cta-actions">
                    <a href="{{ url('/catalog') }}" class="btn btn-dark">До каталогу</a>
                    <a href="{{ url('/cooperation') }}" class="btn btn-light">Співробітництво</a>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
