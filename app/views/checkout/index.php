<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php
$cartItems = $data['cartItems'] ?? [];
$total = $data['total'] ?? 0;
$errors = $data['errors'] ?? [];
$old = $data['old'] ?? [];
?>

<main class="checkout-page">
    <section class="catalog-hero">
        <div class="container">
            <div class="catalog-hero-box">
                <span class="hero-badge">CHECKOUT</span>
                <h1>Оформлення замовлення</h1>
                <p>Заповни дані для зв’язку, доставки та оплати.</p>
            </div>
        </div>
    </section>

    <section class="checkout-section">
        <div class="container">
            <div class="checkout-layout">
                <form action="index.php?url=checkout-store" method="POST" class="checkout-form-box">
                    <h2>Дані покупця</h2>

                    <div class="checkout-grid">
                        <div class="form-group">
                            <label for="full_name">Ім’я та прізвище</label>
                            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($old['full_name'] ?? ''); ?>">
                            <?php if (!empty($errors['full_name'])): ?><small class="form-error"><?= htmlspecialchars($errors['full_name']); ?></small><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="phone">Телефон</label>
                            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($old['phone'] ?? ''); ?>">
                            <?php if (!empty($errors['phone'])): ?><small class="form-error"><?= htmlspecialchars($errors['phone']); ?></small><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? ''); ?>">
                            <?php if (!empty($errors['email'])): ?><small class="form-error"><?= htmlspecialchars($errors['email']); ?></small><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="city">Місто</label>
                            <input type="text" id="city" name="city" value="<?= htmlspecialchars($old['city'] ?? ''); ?>">
                            <?php if (!empty($errors['city'])): ?><small class="form-error"><?= htmlspecialchars($errors['city']); ?></small><?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address_line">Адреса або відділення</label>
                        <input type="text" id="address_line" name="address_line" value="<?= htmlspecialchars($old['address_line'] ?? ''); ?>">
                        <?php if (!empty($errors['address_line'])): ?><small class="form-error"><?= htmlspecialchars($errors['address_line']); ?></small><?php endif; ?>
                    </div>

                    <div class="checkout-grid">
                        <div class="form-group">
                            <label for="delivery_method">Спосіб доставки</label>
                            <select id="delivery_method" name="delivery_method">
                                <option value="nova_poshta" <?= (($old['delivery_method'] ?? '') === 'nova_poshta') ? 'selected' : ''; ?>>Нова пошта</option>
                                <option value="ukrposhta" <?= (($old['delivery_method'] ?? '') === 'ukrposhta') ? 'selected' : ''; ?>>Укрпошта</option>
                                <option value="courier" <?= (($old['delivery_method'] ?? '') === 'courier') ? 'selected' : ''; ?>>Кур’єр</option>
                                <option value="pickup" <?= (($old['delivery_method'] ?? '') === 'pickup') ? 'selected' : ''; ?>>Самовивіз</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="payment_method">Спосіб оплати</label>
                            <select id="payment_method" name="payment_method">
                                <option value="cash_on_delivery" <?= (($old['payment_method'] ?? '') === 'cash_on_delivery') ? 'selected' : ''; ?>>Післяплата</option>
                                <option value="card" <?= (($old['payment_method'] ?? '') === 'card') ? 'selected' : ''; ?>>Оплата карткою</option>
                                <option value="bank_transfer" <?= (($old['payment_method'] ?? '') === 'bank_transfer') ? 'selected' : ''; ?>>Банківський переказ</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="comment">Коментар до замовлення</label>
                        <textarea id="comment" name="comment" rows="5"><?= htmlspecialchars($old['comment'] ?? ''); ?></textarea>
                    </div>

                    <div class="checkout-form-actions">
                        <button type="submit" class="btn btn-dark checkout-submit-btn">Підтвердити замовлення</button>
                    </div>
                </form>

                <aside class="checkout-summary-box">
                    <h2>Твоє замовлення</h2>

                    <div class="checkout-summary-list">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="checkout-summary-item">
                                <div>
                                    <strong><?= htmlspecialchars($item['name']); ?></strong>
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
                                <strong><?= number_format((float)$item['price'] * (int)$item['quantity'], 0, '.', ' '); ?> грн</strong>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="checkout-total-line">
                        <span>Разом</span>
                        <strong><?= number_format((float)$total, 0, '.', ' '); ?> грн</strong>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>