<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php
$order = $data['order'];
$items = $data['items'] ?? [];
?>

<main class="checkout-page">
    <section class="checkout-success-section">
        <div class="container">
            <div class="checkout-success-box">
                <span class="hero-badge">ORDER SUCCESS</span>
                <h1>Замовлення оформлено</h1>
                <p>Дякуємо! Ми отримали твоє замовлення та скоро зв’яжемося з тобою.</p>

                <div class="checkout-success-info">
                    <div><strong>Номер замовлення:</strong> #<?= (int)$order['id']; ?></div>
                    <div><strong>Сума:</strong> <?= number_format((float)$order['total_amount'], 0, '.', ' '); ?> грн</div>
                    <div><strong>Статус:</strong> <?= htmlspecialchars($order['status']); ?></div>
                </div>

                <div class="checkout-success-items">
                    <?php foreach ($items as $item): ?>
                        <div class="checkout-success-item">
                            <div>
                                <strong><?= htmlspecialchars($item['product_name']); ?></strong>
                                <div class="checkout-summary-meta">
                                    К-сть: <?= (int)$item['quantity']; ?>
                                    <?php if (!empty($item['selected_size'])): ?>
                                        • Розмір: <?= htmlspecialchars($item['selected_size']); ?>
                                    <?php endif; ?>
                                    <?php if (!empty($item['selected_color_name'])): ?>
                                        • Колір: <?= htmlspecialchars($item['selected_color_name']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <strong><?= number_format((float)$item['product_price'] * (int)$item['quantity'], 0, '.', ' '); ?> грн</strong>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="checkout-success-actions">
                    <a href="index.php" class="btn btn-light">На головну</a>
                    <a href="index.php?url=catalog" class="btn btn-dark">Продовжити покупки</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>