<section class="auth-box">
    <h2>Реєстрація</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="POST" class="form">
        <input type="text" name="name" placeholder="Ім'я" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit" class="btn">Зареєструватися</button>
    </form>
</section>