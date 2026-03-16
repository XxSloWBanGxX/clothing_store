<section class="product-page">
    <div class="product-image">
        <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
    </div>

    <div class="product-info">
        <h1><?= htmlspecialchars($product['title']) ?></h1>
        <p class="product-category">Категорія: <?= htmlspecialchars($product['category']) ?></p>
        <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>
        <div class="product-price"><?= $product['price'] ?> грн</div>

        <a class="btn" href="<?= BASE_URL ?>cart/add?id=<?= $product['id'] ?>">Додати в кошик</a>
    </div>
</section>