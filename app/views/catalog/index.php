<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="catalog-page">
    <section class="catalog-hero">
        <div class="container">
            <div class="catalog-hero-box">
                <span class="hero-badge">CATALOG</span>
                <h1>Каталог товарів</h1>
                <p>Знайди потрібний товар через фільтри, категорії та зручний перегляд.</p>
            </div>
        </div>
    </section>

    <section class="catalog-section">
        <div class="container catalog-layout">
            <aside class="catalog-sidebar">
                <form method="GET" action="index.php" class="filter-box">
                    <input type="hidden" name="url" value="catalog">

                    <div class="filter-group">
                        <label for="search">Пошук</label>
                        <input type="text" id="search" name="search" placeholder="Назва товару..." value="<?= htmlspecialchars($data['filters']['search'] ?? ''); ?>">
                    </div>

                    <div class="filter-group">
                        <label for="category">Категорія</label>
                        <select id="category" name="category">
                            <option value="">Усі категорії</option>
                            <?php foreach ($data['categories'] as $category): ?>
                                <option value="<?= htmlspecialchars($category['slug']); ?>" <?= (($data['filters']['category'] ?? '') === $category['slug']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-grid-two">
                        <div class="filter-group">
                            <label for="min_price">Ціна від</label>
                            <input type="number" id="min_price" name="min_price" placeholder="0" value="<?= htmlspecialchars($data['filters']['min_price'] ?? ''); ?>">
                        </div>

                        <div class="filter-group">
                            <label for="max_price">Ціна до</label>
                            <input type="number" id="max_price" name="max_price" placeholder="5000" value="<?= htmlspecialchars(($data['filters']['max_price'] !== '' ? $data['filters']['max_price'] : '') ?? ''); ?>">
                        </div>
                    </div>

                    <div class="filter-group">
                        <label for="max_price_range">Повзунок ціни до</label>
                        <input type="range" id="max_price_range" min="0" max="5000" step="100" value="<?= htmlspecialchars(($data['filters']['max_price'] !== '' ? $data['filters']['max_price'] : 5000) ?? 5000); ?>">
                        <div class="range-value">
                            До: <span id="maxPriceValue"><?= htmlspecialchars(($data['filters']['max_price'] !== '' ? $data['filters']['max_price'] : 5000) ?? 5000); ?></span> грн
                        </div>
                    </div>

                    <div class="filter-group">
                        <label for="sort">Сортування</label>
                        <select id="sort" name="sort">
                            <option value="newest" <?= (($data['filters']['sort'] ?? '') === 'newest') ? 'selected' : ''; ?>>Спочатку нові</option>
                            <option value="price_asc" <?= (($data['filters']['sort'] ?? '') === 'price_asc') ? 'selected' : ''; ?>>Ціна: від дешевих</option>
                            <option value="price_desc" <?= (($data['filters']['sort'] ?? '') === 'price_desc') ? 'selected' : ''; ?>>Ціна: від дорогих</option>
                            <option value="name_asc" <?= (($data['filters']['sort'] ?? '') === 'name_asc') ? 'selected' : ''; ?>>За назвою</option>
                        </select>
                    </div>

                    <div class="filter-checkbox">
                        <label>
                            <input type="checkbox" name="in_stock" value="1" <?= !empty($data['filters']['in_stock']) ? 'checked' : ''; ?>>
                            Тільки в наявності
                        </label>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-dark">Застосувати</button>
                        <a href="index.php?url=catalog" class="btn btn-light">Скинути</a>
                    </div>
                </form>
            </aside>

            <div class="catalog-content">
                <div class="catalog-toolbar">
                    <div>
                        <h2>Товари</h2>
                        <p>Знайдено: <?= (int)$data['totalProducts']; ?></p>
                    </div>
                </div>

                <?php if (!empty($data['products'])): ?>
                    <div class="catalog-grid">
                        <?php foreach ($data['products'] as $product): ?>
                            <div class="catalog-card">
                                <a href="index.php?url=product&id=<?= (int)$product['id']; ?>" class="catalog-card-link">
                                    <div class="catalog-card-image">
                                        <?php if (!empty($product['image'])): ?>
                                            <img
                                                src="assets/images/products/<?= htmlspecialchars($product['image']); ?>"
                                                alt="<?= htmlspecialchars($product['name']); ?>"
                                                class="catalog-product-image"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                            >
                                            <div class="product-image-placeholder image-fallback" style="display:none;">
                                                <?= htmlspecialchars($product['name']); ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="product-image-placeholder">
                                                <?= htmlspecialchars($product['name']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="catalog-card-info">
                                        <h3><?= htmlspecialchars($product['name']); ?></h3>
                                        <p class="catalog-card-price"><?= number_format((float)$product['price'], 0, '.', ' '); ?> грн</p>
                                        <p class="catalog-stock <?= (int)$product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                                            <?= (int)$product['stock'] > 0 ? 'Є в наявності' : 'Немає в наявності'; ?>
                                        </p>
                                    </div>
                                </a>

                                <div class="catalog-card-menu">
                                    <details class="card-menu">
                                        <summary>•••</summary>
                                        <div class="card-menu-dropdown">
                                            <form action="index.php?url=add-to-cart" method="POST">
                                                <input type="hidden" name="product_id" value="<?= (int)$product['id']; ?>">
                                                <button type="submit">Додати в кошик</button>
                                            </form>

                                            <form action="index.php?url=add-to-favorites" method="POST" class="favorite-folder-form">
                                                <input type="hidden" name="product_id" value="<?= (int)$product['id']; ?>">

                                                <label for="folder_<?= (int)$product['id']; ?>">Папка</label>
                                                <select name="folder" id="folder_<?= (int)$product['id']; ?>" class="favorite-folder-select">
                                                    <?php foreach ($data['favoriteFolders'] as $folder): ?>
                                                        <option value="<?= htmlspecialchars($folder); ?>">
                                                            <?= htmlspecialchars($folder); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <button type="submit">Додати в обране</button>
                                            </form>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($data['totalPages'] > 1): ?>
                        <div class="pagination">
                            <?php
                            $baseParams = [
                                'url' => 'catalog',
                                'search' => $data['filters']['search'] ?? '',
                                'category' => $data['filters']['category'] ?? '',
                                'min_price' => $data['filters']['min_price'] ?? '',
                                'max_price' => $data['filters']['max_price'] ?? '',
                                'sort' => $data['filters']['sort'] ?? '',
                            ];

                            if (!empty($data['filters']['in_stock'])) {
                                $baseParams['in_stock'] = 1;
                            }
                            ?>

                            <?php if ($data['currentPage'] > 1): ?>
                                <?php $prevParams = $baseParams; $prevParams['page'] = $data['currentPage'] - 1; ?>
                                <a href="index.php?<?= http_build_query($prevParams); ?>" class="pagination-link">←</a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
                                <?php $pageParams = $baseParams; $pageParams['page'] = $i; ?>
                                <a href="index.php?<?= http_build_query($pageParams); ?>" class="pagination-link <?= $i === $data['currentPage'] ? 'active' : ''; ?>">
                                    <?= $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($data['currentPage'] < $data['totalPages']): ?>
                                <?php $nextParams = $baseParams; $nextParams['page'] = $data['currentPage'] + 1; ?>
                                <a href="index.php?<?= http_build_query($nextParams); ?>" class="pagination-link">→</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-box">
                        <h3>Товарів не знайдено</h3>
                        <p>Спробуй змінити фільтри або очистити пошук.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>