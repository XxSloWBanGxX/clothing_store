<section class="hero">
    <div class="hero-content">
        <h1>Сучасний онлайн-магазин одягу</h1>
        <p>Стильні речі для тих, хто хоче виглядати впевнено щодня.</p>
        <a href="#catalog" class="btn">Перейти в каталог</a>
    </div>
</section>

<section id="catalog">
    <h2 class="section-title">Каталог товарів</h2>

    <div class="grid">
        <?php foreach ($products as $product): ?>
            <div class="card">
                <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                <div class="card-body">
                    <h3><?= htmlspecialchars($product['title']) ?></h3>
                    <p><?= htmlspecialchars($product['category']) ?></p>
                    <div class="price-row">
                        <span class="price"><?= $product['price'] ?> грн</span>
                    </div>
                    <div class="card-actions">
                        <a class="btn-outline" href="<?= BASE_URL ?>product?id=<?= $product['id'] ?>">Детальніше</a>
                        <a class="btn" href="<?= BASE_URL ?>cart/add?id=<?= $product['id'] ?>">У кошик</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>