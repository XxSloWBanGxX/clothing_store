<?php require_once __DIR__ . '/layout.php'; ?>

<?php $order = $data['order']; ?>

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Замовлення #<?= (int)$order['id']; ?></h2>
    </div>

    <div class="admin-order-grid">
        <div class="admin-order-card">
            <h3>Інформація про клієнта</h3>
            <p><strong>Користувач:</strong> <?= htmlspecialchars($order['username'] ?? '—'); ?></p>
            <p><strong>Ім’я:</strong> <?= htmlspecialchars($order['full_name']); ?></p>
            <p><strong>Телефон:</strong> <?= htmlspecialchars($order['phone']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['email']); ?></p>
            <p><strong>Місто:</strong> <?= htmlspecialchars($order['city']); ?></p>
            <p><strong>Адреса / відділення:</strong> <?= htmlspecialchars($order['address_line']); ?></p>
            <p><strong>Доставка:</strong> <?= htmlspecialchars($order['delivery_method']); ?></p>
            <p><strong>Оплата:</strong> <?= htmlspecialchars($order['payment_method']); ?></p>
            <p><strong>Коментар:</strong> <?= htmlspecialchars($order['comment'] ?: '—'); ?></p>
        </div>

        <div class="admin-order-card">
            <h3>Статус замовлення</h3>

            <form action="index.php?url=admin-order-update-status" method="POST" class="admin-order-status-form">
                <input type="hidden" name="id" value="<?= (int)$order['id']; ?>">

                <div class="form-group">
                    <label for="status">Статус</label>
                    <select id="status" name="status">
                        <option value="new" <?= $order['status'] === 'new' ? 'selected' : ''; ?>>new</option>
                        <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : ''; ?>>processing</option>
                        <option value="sent" <?= $order['status'] === 'sent' ? 'selected' : ''; ?>>sent</option>
                        <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : ''; ?>>completed</option>
                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : ''; ?>>cancelled</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-dark">Оновити статус</button>
            </form>

            <div class="admin-order-total">
                <strong>Разом:</strong> <?= number_format((float)$order['total_amount'], 0, '.', ' '); ?> грн
            </div>
        </div>
    </div>

    <div class="admin-order-card admin-order-items-card">
        <h3>Товари в замовленні</h3>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Ціна</th>
                        <th>К-сть</th>
                        <th>Розмір</th>
                        <th>Колір</th>
                        <th>Сума</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']); ?></td>
                            <td><?= number_format((float)$item['product_price'], 0, '.', ' '); ?> грн</td>
                            <td><?= (int)$item['quantity']; ?></td>
                            <td><?= htmlspecialchars($item['selected_size'] ?: '—'); ?></td>
                            <td><?= htmlspecialchars($item['selected_color_name'] ?: '—'); ?></td>
                            <td><?= number_format((float)$item['product_price'] * (int)$item['quantity'], 0, '.', ' '); ?> грн</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-form-actions">
        <a href="index.php?url=admin-orders" class="btn btn-light">Назад до замовлень</a>
    </div>
</section>

</main>
</div>
</body>
</html>