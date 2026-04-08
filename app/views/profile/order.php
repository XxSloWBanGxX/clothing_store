<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php $order = $data['order'] ?? null; ?>

<main class="profile-page">
    <section class="catalog-hero">
        <div class="container">
            <div class="catalog-hero-box">
                <span class="hero-badge">MY ORDER</span>
                <h1>Замовлення #<?= (int)($order['id'] ?? 0); ?></h1>
                <p>Детальна інформація про твоє замовлення.</p>
            </div>
        </div>
    </section>

    <section class="profile-section">
        <div class="container">
            <?php if ($order): ?>
                <div class="profile-order-details-grid">
                    <div class="profile-card">
                        <h2>Інформація про замовлення</h2>
                        <div class="profile-info-list">
                            <div class="profile-info-item">
                                <span>Статус</span>
                                <strong><?= htmlspecialchars($order['status']); ?></strong>
                            </div>
                            <div class="profile-info-item">
                                <span>Дата</span>
                                <strong><?= htmlspecialchars($order['created_at']); ?></strong>
                            </div>
                            <div class="profile-info-item">
                                <span>Ім’я</span>
                                <strong><?= htmlspecialchars($order['full_name']); ?></strong>
                            </div>
                            <div class="profile-info-item">
                                <span>Телефон</span>
                                <strong><?= htmlspecialchars($order['phone']); ?></strong>
                            </div>
                            <div class="profile-info-item">
                                <span>Email</span>
                                <strong><?= htmlspecialchars($order['email']); ?></strong>
                            </div>
                            <div class="profile-info-item">
                                <span>Місто</span>
                                <strong><?= htmlspecialchars($order['city']); ?></strong>
                            </div>
                            <div class="profile-info-item">
                                <span>Адреса / відділення</span>
                                <strong><?= htmlspecialchars($order['address_line']); ?></strong>
                            </div>
                            <div class="profile-info-item">
                                <span>Доставка</span>
                                <strong><?= htmlspecialchars($order['delivery_method']); ?></strong>
                            </div>
                            <div class="profile-info-item">
                                <span>Оплата</span>
                                <strong><?= htmlspecialchars($order['payment_method']); ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="profile-card">
                        <h2>Товари в замовленні</h2>

                        <div class="profile-order-products">
                            <?php foreach (($order['items'] ?? []) as $item): ?>
                                <div class="profile-order-product-item">
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

                        <div class="checkout-total-line">
                            <span>Разом</span>
                            <strong><?= number_format((float)$order['total_amount'], 0, '.', ' '); ?> грн</strong>
                        </div>
                    </div>
                </div>

                <div class="admin-form-actions">
                    <a href="index.php?url=profile" class="btn btn-light">Назад у профіль</a>
                </div>
            <?php else: ?>
                <div class="empty-box">
                    <h3>Замовлення не знайдено</h3>
                    <a href="index.php?url=profile" class="btn btn-dark">Повернутись у профіль</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>