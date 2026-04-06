<?php require_once __DIR__ . '/layout.php'; ?>

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Замовлення</h2>
    </div>

    <?php if (!empty($data['orders'])): ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Користувач</th>
                        <th>Ім’я</th>
                        <th>Телефон</th>
                        <th>Сума</th>
                        <th>Статус</th>
                        <th>Дата</th>
                        <th>Дія</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['orders'] as $order): ?>
                        <tr>
                            <td>#<?= (int)$order['id']; ?></td>
                            <td><?= htmlspecialchars($order['username'] ?? '—'); ?></td>
                            <td><?= htmlspecialchars($order['full_name']); ?></td>
                            <td><?= htmlspecialchars($order['phone']); ?></td>
                            <td><?= number_format((float)$order['total_amount'], 0, '.', ' '); ?> грн</td>
                            <td><?= htmlspecialchars($order['status']); ?></td>
                            <td><?= htmlspecialchars($order['created_at']); ?></td>
                            <td>
                                <a href="index.php?url=admin-order-show&id=<?= (int)$order['id']; ?>" class="btn btn-light btn-sm">Переглянути</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-box">
            <h3>Замовлень ще немає</h3>
            <p>Коли користувачі почнуть оформляти замовлення, вони з’являться тут.</p>
        </div>
    <?php endif; ?>
</section>

</main>
</div>
</body>
</html>