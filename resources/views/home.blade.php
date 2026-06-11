
@extends('layouts.app')

@section('title', 'Головна - CLOTHSTORE')

@section('content')

<main>
    <section class="hero-section">
        <div class="container hero-grid">
            <div class="hero-content">
                <span class="hero-badge">NEW COLLECTION</span>
                <h1>Стиль, який<br>говорить за тебе</h1>
                <p>
                    Сучасний одяг для тих, хто цінує мінімалізм, комфорт та
                    впевнений вигляд кожного дня.
                </p>

                <div class="hero-buttons">
                    <a href="index.php?url=catalog" class="btn btn-dark">Перейти в каталог</a>
                    <a href="index.php?url=catalog" class="btn btn-light">Дивитися новинки</a>
                </div>

                <div class="hero-stats">
                    <div class="stat-item">
                        <h3>500+</h3>
                        <p>Товарів</p>
                    </div>
                    <div class="stat-item">
                        <h3>24/7</h3>
                        <p>Онлайн замовлення</p>
                    </div>
                    <div class="stat-item">
                        <h3>100%</h3>
                        <p>Сучасний стиль</p>
                    </div>
                </div>
            </div>

            <div class="hero-visual">
                <div class="hero-card-main">
                    <div class="hero-image-placeholder">FASHION</div>
                </div>
                <div class="hero-floating-card top-card">Minimal</div>
                <div class="hero-floating-card bottom-card">Streetwear</div>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="container features-grid">
            <div class="feature-box">
                <h3>Швидке оформлення</h3>
                <p>Простий і зручний процес покупки без зайвих кроків.</p>
            </div>
            <div class="feature-box">
                <h3>Актуальні колекції</h3>
                <p>Стильні моделі одягу в сучасному мінімалістичному стилі.</p>
            </div>
            <div class="feature-box">
                <h3>Зручний інтерфейс</h3>
                <p>Приємна навігація, сучасний дизайн і база для розвитку магазину.</p>
            </div>
        </div>
    </section>

    <section class="products-section">
        <div class="container">
            <div class="section-head">
                <div>
                    <span class="section-label">FEATURED</span>
                    <h2>Популярні товари</h2>
                </div>
                <a href="index.php?url=catalog" class="section-link">Дивитися все</a>
            </div>

            <div class="products-grid">
                <?php if (!empty($data['featuredProducts'])): ?>
                    <?php foreach ($data['featuredProducts'] as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <span class="product-tag">
                                    <?= !empty($product['category_name']) ? htmlspecialchars($product['category_name']) : 'New'; ?>
                                </span>

                                <?php if (!empty($product['image'])): ?>
                                    <img
                                        src="assets/images/products/<?= htmlspecialchars($product['image']); ?>"
                                        alt="<?= htmlspecialchars($product['name']); ?>"
                                        class="home-product-image"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                    >
                                    <div class="image-placeholder image-fallback" style="display:none;">
                                        <?= htmlspecialchars($product['name']); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="image-placeholder">
                                        <?= htmlspecialchars($product['name']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="product-info">
                                <h3><?= htmlspecialchars($product['name']); ?></h3>
                                <p class="product-price">
                                    <?= number_format((float)$product['price'], 0, '.', ' '); ?> грн
                                </p>
                                <a href="index.php?url=product&id=<?= (int)$product['id']; ?>" class="btn btn-small">
                                    Детальніше
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-box">
                        <h3>Поки що немає товарів</h3>
                        <p>Додай товари в базу даних, і вони з’являться тут.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="banner-section">
        <div class="container">
            <div class="banner-box">
                <div>
                    <span class="section-label">COLLECTION</span>
                    <h2>Онови свій гардероб вже сьогодні</h2>
                    <p>
                        Підбери речі, які підкреслять твій стиль та зроблять
                        магазин виглядом реально сучасним уже з головної сторінки.
                    </p>
                </div>
                <a href="index.php?url=catalog" class="btn btn-dark">До покупок</a>
            </div>
        </div>
    </section>
</main>

@endsection