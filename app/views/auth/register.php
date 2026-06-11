<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="auth-page">
    <div class="container">
        <div class="auth-wrapper">
            <div class="auth-card">
                <span class="hero-badge">REGISTER</span>
                <h1 class="auth-title">Створення акаунта</h1>
                <p class="auth-subtitle">Заповни дані для створення профілю.</p>

                <form action="index.php?url=register-post" method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="name">Імʼя</label>
                        <input type="text" id="name" name="name" placeholder="Введи імʼя"
                               value="<?= htmlspecialchars($data['old']['name'] ?? ''); ?>">
                        <?php if (!empty($data['errors']['name'])): ?>
                            <small class="form-error"><?= htmlspecialchars($data['errors']['name']); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Наприклад: bogdan_01"
                               value="<?= htmlspecialchars($data['old']['username'] ?? ''); ?>">
                        <?php if (!empty($data['errors']['username'])): ?>
                            <small class="form-error"><?= htmlspecialchars($data['errors']['username']); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Введи email"
                               value="<?= htmlspecialchars($data['old']['email'] ?? ''); ?>">
                        <?php if (!empty($data['errors']['email'])): ?>
                            <small class="form-error"><?= htmlspecialchars($data['errors']['email']); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="phone">Номер телефону</label>
                        <input type="text" id="phone" name="phone" placeholder="+380..."
                               value="<?= htmlspecialchars($data['old']['phone'] ?? ''); ?>">
                        <?php if (!empty($data['errors']['phone'])): ?>
                            <small class="form-error"><?= htmlspecialchars($data['errors']['phone']); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" id="password" name="password" placeholder="Мінімум 6 символів">
                        <?php if (!empty($data['errors']['password'])): ?>
                            <small class="form-error"><?= htmlspecialchars($data['errors']['password']); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Підтвердження пароля</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Повтори пароль">
                        <?php if (!empty($data['errors']['confirm_password'])): ?>
                            <small class="form-error"><?= htmlspecialchars($data['errors']['confirm_password']); ?></small>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-dark auth-btn">Зареєструватися</button>
                </form>

                <p class="auth-switch">
                    Уже маєш акаунт?
                    <a href="index.php?url=login">Увійти</a>
                </p>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>