<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<?php
$user = $data['user'] ?? [];
$orders = $data['orders'] ?? [];
$passwordErrors = $data['passwordErrors'] ?? [];
$passwordSuccess = $data['passwordSuccess'] ?? '';
?>

<main class="profile-page">
    <section class="catalog-hero">
        <div class="container">
            <div class="catalog-hero-box">
                <span class="hero-badge">PROFILE</span>
                <h1>Мій профіль</h1>
                <p>Переглядай свої дані, змінюй пароль і відстежуй замовлення.</p>
            </div>
        </div>
    </section>

    <section class="profile-section">
        <div class="container">
            <div class="profile-grid">
                <div class="profile-card">
                    <h2>Особисті дані</h2>
                    <div class="profile-info-list">
                        <div class="profile-info-item">
                            <span>Ім’я</span>
                            <strong><?= htmlspecialchars($user['name'] ?? '—'); ?></strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Username</span>
                            <strong><?= htmlspecialchars($user['username'] ?? '—'); ?></strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Email</span>
                            <strong><?= htmlspecialchars($user['email'] ?? '—'); ?></strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Телефон</span>
                            <strong><?= htmlspecialchars($user['phone'] ?? '—'); ?></strong>
                        </div>
                        <div class="profile-info-item">
                            <span>Роль</span>
                            <strong><?= htmlspecialchars($user['role'] ?? 'user'); ?></strong>
                        </div>
                    </div>
                </div>

                <div class="profile-card">
                    <h2>Змінити пароль</h2>

                    <?php if (!empty($passwordSuccess)): ?>
                        <div class="profile-success-message"><?= htmlspecialchars($passwordSuccess); ?></div>
                    <?php endif; ?>

                    <form action="index.php?url=profile-change-password" method="POST" class="profile-password-form">
                        <div class="form-group">
                            <label for="current_password">Поточний пароль</label>
                            <input type="password" id="current_password" name="current_password">
                            <?php if (!empty($passwordErrors['current_password'])): ?>
                                <small class="form-error"><?= htmlspecialchars($passwordErrors['current_password']); ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="new_password">Новий пароль</label>
                            <input type="password" id="new_password" name="new_password">
                            <?php if (!empty($passwordErrors['new_password'])): ?>
                                <small class="form-error"><?= htmlspecialchars($passwordErrors['new_password']); ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Підтверди новий пароль</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                            <?php if (!empty($passwordErrors['confirm_password'])): ?>
                                <small class="form-error"><?= htmlspecialchars($passwordErrors['confirm_password']); ?></small>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-dark profile-password-btn">Оновити пароль</button>
                    </form>
                </div>
            </div>

            <div class="profile-orders-box">
                <div class="section-head">
                    <div>
                        <span class="section-label">MY ORDERS</span>
                        <h2>Мої замовлення</h2>
                    </div>
                </div>

                <?php if (!empty($orders)): ?>
                    <div class="profile-orders-list">
                        <?php foreach ($orders as $order): ?>
                            <div class="profile-order-item">
                                <div class="profile-order-main">
                                    <strong>Замовлення #<?= (int)$order['id']; ?></strong>
                                    <span class="profile-order-status"><?= htmlspecialchars($order['status']); ?></span>
                                </div>

                                <div class="profile-order-meta">
                                    <span><?= htmlspecialchars($order['created_at']); ?></span>
                                    <strong><?= number_format((float)$order['total_amount'], 0, '.', ' '); ?> грн</strong>
                                </div>

                                <a href="index.php?url=profile-order&id=<?= (int)$order['id']; ?>" class="btn btn-light">Переглянути</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-box">
                        <h3>У тебе ще немає замовлень</h3>
                        <p>Коли ти оформиш замовлення, воно з’явиться тут.</p>
                        <a href="index.php?url=catalog" class="btn btn-dark">Перейти в каталог</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>