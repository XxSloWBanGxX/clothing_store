<h2 class="section-title">Кошик</h2>

<?php if (empty($cart)): ?>
    <p>Ваш кошик порожній.</p>
<?php else: ?>
    <div class="cart-list">
        <?php $total = 0; ?>
        <?php foreach ($cart as $index => $item): ?>
            <?php $total += $item['price']; ?>
            <div class="cart-item">
                <div>
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                    <p><?= $item['price'] ?> грн</p>
                </div>
                <a class="btn-danger" href="<?= BASE_URL ?>cart/remove?index=<?= $index ?>">Видалити</a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="cart-total">
        <strong>Загальна сума: <?= $total ?> грн</strong>
    </div>
<?php endif; ?>