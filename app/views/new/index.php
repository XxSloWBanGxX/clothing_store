<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php $products = $data['products'] ?? []; ?>

<main class="new-page">
    <section class="new-hero">
        <div class="container">
            <div class="new-hero-grid">
                <div class="new-hero-content reveal-up">
                    <span class="hero-badge">NEW DROP</span>
                    <h1>Новинки, які задають стиль</h1>
                    <p>
                        Відкрий нову колекцію ClothStore — сучасний одяг, мінімалістичний стиль
                        та акцент на деталях. Ми зібрали моделі, які виглядають свіжо,
                        легко комбінуються і підходять для щоденного образу.
                    </p>

                    <div class="new-hero-actions">
                        <a href="index.php?url=catalog" class="btn btn-dark">Перейти в каталог</a>
                        <a href="#new-drop-grid" class="btn btn-light">Дивитись новинки</a>
                    </div>

                    <div class="new-hero-points">
                        <div class="new-hero-point">
                            <strong>Fresh</strong>
                            <span>нові позиції</span>
                        </div>
                        <div class="new-hero-point">
                            <strong>Style</strong>
                            <span>сучасний мінімалізм</span>
                        </div>
                        <div class="new-hero-point">
                            <strong>Trend</strong>
                            <span>актуальні кольори</span>
                        </div>
                    </div>
                </div>

                <div class="new-hero-visual reveal-scale">
                    <div class="new-hero-card main-card">
                        <div class="new-hero-title">CLOTHSTORE</div>
                    </div>
                    <div class="new-floating-chip chip-1">New season</div>
                    <div class="new-floating-chip chip-2">Minimal look</div>
                    <div class="new-floating-chip chip-3">Urban style</div>
                </div>
            </div>
        </div>
    </section>

    <section class="new-marquee-section">
        <div class="new-marquee">
            <div class="new-marquee-track">
                <span>NEW ARRIVALS</span>
                <span>MODERN STREETWEAR</span>
                <span>MINIMAL FASHION</span>
                <span>STYLE • COMFORT • MOOD</span>
                <span>NEW ARRIVALS</span>
                <span>MODERN STREETWEAR</span>
                <span>MINIMAL FASHION</span>
                <span>STYLE • COMFORT • MOOD</span>
            </div>
        </div>
    </section>

    <section class="new-highlight-section">
        <div class="container">
            <div class="new-highlight-grid">
                <div class="new-highlight-box large reveal-left">
                    <span class="section-label">NEW COLLECTION</span>
                    <h2>Свіжий погляд на базовий гардероб</h2>
                    <p>
                        Ми робимо ставку на чисті форми, універсальні відтінки та зручний крій.
                        Новинки ClothStore легко поєднуються між собою та підходять як
                        для повсякденного стилю, так і для більш виразних образів.
                    </p>
                </div>

                <div class="new-highlight-box small reveal-right">
                    <h3>01</h3>
                    <p>Нові моделі в сучасному стилі</p>
                </div>

                <div class="new-highlight-box small reveal-right delay-1">
                    <h3>02</h3>
                    <p>Трендові кольори та універсальні розміри</p>
                </div>
            </div>
        </div>
    </section>

    <section class="new-products-section" id="new-drop-grid">
        <div class="container">
            <div class="section-head">
                <div>
                    <span class="section-label">JUST IN</span>
                    <h2>Головні новинки зараз</h2>
                </div>
                <a href="index.php?url=catalog" class="section-link">Увесь каталог</a>
            </div>

            <?php if (!empty($products)): ?>
                <div class="new-products-grid">
                    <?php foreach ($products as $index => $product): ?>
                        <a href="index.php?url=product&id=<?= (int)$product['id']; ?>" class="new-product-card reveal-up delay-<?= $index % 4; ?>">
                            <div class="new-product-image-wrap">
                                <span class="new-product-badge">NEW</span>

                                <?php if (!empty($product['image'])): ?>
                                    <img
                                        src="assets/images/products/<?= htmlspecialchars($product['image']); ?>"
                                        alt="<?= htmlspecialchars($product['name']); ?>"
                                        class="new-product-image"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                    >
                                    <div class="new-product-fallback" style="display:none;">
                                        <?= htmlspecialchars($product['name']); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="new-product-fallback">
                                        <?= htmlspecialchars($product['name']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="new-product-info">
                                <h3><?= htmlspecialchars($product['name']); ?></h3>
                                <p class="new-product-category"><?= htmlspecialchars($product['category_name'] ?? 'Одяг'); ?></p>
                                <div class="new-product-bottom">
                                    <strong><?= number_format((float)$product['price'], 0, '.', ' '); ?> грн</strong>
                                    <span class="new-product-arrow">→</span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-box">
                    <h3>Новинки ще не додані</h3>
                    <p>Додай популярні товари в адмінці, і вони з’являться тут.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="new-banner-section">
        <div class="container">
            <div class="new-banner-box reveal-up">
                <div>
                    <span class="section-label">CLOTHSTORE MOOD</span>
                    <h2>Стиль, який відчувається з першого погляду</h2>
                    <p>
                        Ми поєднуємо мінімалізм, комфорт і візуально чистий дизайн,
                        щоб кожна нова колекція виглядала цілісно та актуально.
                    </p>
                </div>

                <div class="new-banner-actions">
                    <a href="index.php?url=about" class="btn btn-light">Більше про нас</a>
                    <a href="index.php?url=catalog" class="btn btn-dark">Купити зараз</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>