<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="favorites-page">
    <section class="catalog-hero">
        <div class="container">
            <div class="catalog-hero-box">
                <span class="hero-badge">FAVORITES</span>
                <h1>Списки бажань</h1>
                <p>Створюй папки та розкладай товари так, як тобі зручно.</p>
            </div>
        </div>
    </section>

    <section class="favorites-section">
        <div class="container">
            <div class="wishlist-header-bar">
                <form action="index.php?url=favorites-create-folder" method="POST" class="wishlist-create-form">
                    <input type="text" name="folder_name" placeholder="Нова папка..." required>
                    <button type="submit" class="wishlist-add-btn">+</button>
                </form>
            </div>

            <div class="wishlist-folder-list">
                <?php foreach ($data['foldersData'] as $folderName => $items): ?>
                    <div class="wishlist-folder-card">
                        <div class="wishlist-folder-top">
                            <div>
                                <h2><?= htmlspecialchars($folderName); ?><?= $folderName === 'Обране' ? ' <span class="wishlist-main-badge">(Основний)</span>' : ''; ?></h2>
                                <p>Кількість товарів: <?= count($items); ?></p>
                            </div>

                            <div class="wishlist-folder-controls">
                                <form action="index.php?url=favorites-clear-folder" method="POST">
                                    <input type="hidden" name="folder" value="<?= htmlspecialchars($folderName); ?>">
                                    <button type="submit" class="wishlist-icon-btn">↻</button>
                                </form>

                                <?php if ($folderName !== 'Обране'): ?>
                                    <form action="index.php?url=favorites-delete-folder" method="POST" onsubmit="return confirm('Видалити папку?');">
                                        <input type="hidden" name="folder" value="<?= htmlspecialchars($folderName); ?>">
                                        <button type="submit" class="wishlist-icon-btn">⋮</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($items)): ?>
                            <div class="wishlist-preview-row">
                                <?php foreach (array_slice($items, 0, 6) as $product): ?>
                                    <a href="index.php?url=product&id=<?= (int)$product['id']; ?>" class="wishlist-preview-item">
                                        <?php if (!empty($product['image'])): ?>
                                            <img
                                                src="assets/images/products/<?= htmlspecialchars($product['image']); ?>"
                                                alt="<?= htmlspecialchars($product['name']); ?>"
                                                class="wishlist-preview-image"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                            >
                                            <div class="wishlist-preview-fallback" style="display:none;">
                                                <?= htmlspecialchars($product['name']); ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="wishlist-preview-fallback">
                                                <?= htmlspecialchars($product['name']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>

                            <div class="wishlist-products-grid">
                                <?php foreach ($items as $product): ?>
                                    <div class="wishlist-product-mini">
                                        <a href="index.php?url=product&id=<?= (int)$product['id']; ?>" class="wishlist-mini-title">
                                            <?= htmlspecialchars($product['name']); ?>
                                        </a>

                                        <div class="wishlist-mini-actions">
                                            <form action="index.php?url=add-to-cart" method="POST">
                                                <input type="hidden" name="product_id" value="<?= (int)$product['id']; ?>">
                                                <button type="submit" class="btn btn-dark btn-sm">В кошик</button>
                                            </form>

                                            <form action="index.php?url=favorites-remove" method="POST">
                                                <input type="hidden" name="product_id" value="<?= (int)$product['id']; ?>">
                                                <input type="hidden" name="folder" value="<?= htmlspecialchars($folderName); ?>">
                                                <button type="submit" class="btn btn-light btn-sm">Видалити</button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="wishlist-empty-line">Список бажань порожній</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>