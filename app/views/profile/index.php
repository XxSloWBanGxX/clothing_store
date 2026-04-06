<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="profile-page">
    <div class="container">
        <div class="profile-card">
            <span class="hero-badge"><?= strtoupper(htmlspecialchars($data['user']['role'])); ?></span>
            <h1 class="profile-title">Мій профіль</h1>
            <p class="profile-subtitle">Особиста інформація користувача.</p>

            <div class="profile-grid">
                <div class="profile-item">
                    <span>Імʼя</span>
                    <strong><?= htmlspecialchars($data['user']['name']); ?></strong>
                </div>

                <div class="profile-item">
                    <span>Username</span>
                    <strong><?= htmlspecialchars($data['user']['username']); ?></strong>
                </div>

                <div class="profile-item">
                    <span>Email</span>
                    <strong><?= htmlspecialchars($data['user']['email']); ?></strong>
                </div>

                <div class="profile-item">
                    <span>Телефон</span>
                    <strong><?= htmlspecialchars($data['user']['phone']); ?></strong>
                </div>

                <div class="profile-item">
                    <span>Роль</span>
                    <strong><?= htmlspecialchars($data['user']['role']); ?></strong>
                </div>

                <div class="profile-item">
                    <span>Підтвердження</span>
                    <strong><?= !empty($data['user']['is_verified']) ? 'Підтверджено' : 'Не підтверджено'; ?></strong>
                </div>
            </div>

            <div class="profile-actions">
                <?php if ($data['user']['role'] === 'admin'): ?>
                    <a href="#" class="btn btn-dark">Адмін панель</a>
                <?php endif; ?>

                <a href="index.php?url=logout" class="btn btn-light">Вийти</a>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>