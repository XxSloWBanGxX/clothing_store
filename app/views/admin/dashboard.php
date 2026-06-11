<?php require_once __DIR__ . '/layout.php'; ?>

<section class="admin-cards">
    <div class="admin-stat-card">
        <span>Всього товарів</span>
        <strong><?= (int)$data['stats']['products']; ?></strong>
    </div>

    <div class="admin-stat-card">
        <span>В наявності</span>
        <strong><?= (int)$data['stats']['inStock']; ?></strong>
    </div>

    <div class="admin-stat-card">
        <span>Популярні товари</span>
        <strong><?= (int)$data['stats']['featured']; ?></strong>
    </div>

    <div class="admin-stat-card">
        <span>Категорії</span>
        <strong><?= (int)$data['stats']['categories']; ?></strong>
    </div>

    <div class="admin-stat-card">
        <span>Користувачі</span>
        <strong><?= (int)$data['stats']['users']; ?></strong>
    </div>

    <div class="admin-stat-card">
        <span>Підтримка</span>
        <strong><?= (int)$data['stats']['support']; ?></strong>
    </div>
</section>

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Швидкі дії</h2>
    </div>

    <div class="admin-quick-actions">
        <a href="index.php?url=admin-products" class="btn btn-dark">Перейти до товарів</a>
        <a href="index.php?url=admin-create" class="btn btn-light">Додати новий товар</a>
        <a href="index.php?url=admin-users" class="btn btn-light">Переглянути користувачів</a>
        <a href="index.php?url=admin-create-user" class="btn btn-light">Створити користувача</a>
    </div>
</section>

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Огляд панелі</h2>
    </div>
    <p class="admin-text">
        У цій версії адмінки можна керувати товарами, кількістю, галереєю з кількох фото, а також переглядати і створювати користувачів. Роль support уже додана як база під майбутній чат підтримки.
    </p>
</section>

</main>
</div>
</body>
</html>