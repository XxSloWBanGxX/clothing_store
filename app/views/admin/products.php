<?php require_once __DIR__ . '/layout.php'; ?>

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Список товарів</h2>
        <a href="index.php?url=admin-create" class="btn btn-dark">+ Додати товар</a>
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
                <?php if (!empty($data['products'])): ?>
                    <?php foreach ($data['products'] as $product): ?>
                        <tr>
                            <td>#<?= (int)$product['id']; ?></td>
                            <td><?= htmlspecialchars($product['name']); ?></td>
                            <td><?= htmlspecialchars($product['category_name']); ?></td>
                            <td><?= number_format((float)$product['price'], 0, '.', ' '); ?> грн</td>
                            <td><?= (int)$product['stock']; ?></td>
                            <td>
                                <?php if ((int)$product['stock'] > 0): ?>
                                    <span class="admin-badge success">В наявності</span>
                                <?php else: ?>
                                    <span class="admin-badge danger">Немає</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="admin-actions">
                                    <a href="index.php?url=admin-edit&id=<?= (int)$product['id']; ?>" class="btn btn-light btn-sm">Редагувати</a>

                                    <form action="index.php?url=admin-delete" method="POST" onsubmit="return confirm('Видалити товар?');">
                                        <input type="hidden" name="id" value="<?= (int)$product['id']; ?>">
                                        <button type="submit" class="btn btn-dark btn-sm">Видалити</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">Поки що товарів немає.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

</main>
</div>
</body>
</html>