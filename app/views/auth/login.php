<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="auth-page">
    <div class="container">
        <div class="auth-wrapper">
            <div class="auth-card">
                <span class="hero-badge">LOGIN</span>
                <h1 class="auth-title">Вхід в акаунт</h1>
                <p class="auth-subtitle">Увійди через email або username.</p>

                <?php if (!empty($data['errors']['general'])): ?>
                    <div class="alert-error"><?= htmlspecialchars($data['errors']['general']); ?></div>
                <?php endif; ?>

                <form action="index.php?url=login-post" method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="login">Email або username</label>
                        <input
                            type="text"
                            id="login"
                            name="login"
                            placeholder="Введи email або username"
                            value="<?= htmlspecialchars($data['old']['login'] ?? ''); ?>"
                        >
                        <?php if (!empty($data['errors']['login'])): ?>
                            <small class="form-error"><?= htmlspecialchars($data['errors']['login']); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Введи пароль"
                        >
                        <?php if (!empty($data['errors']['password'])): ?>
                            <small class="form-error"><?= htmlspecialchars($data['errors']['password']); ?></small>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-dark auth-btn">Увійти</button>
                </form>

                <p class="auth-switch">
                    Немає акаунта?
                    <a href="index.php?url=register">Зареєструватися</a>
                </p>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>