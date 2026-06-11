<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($data['title']) ? htmlspecialchars($data['title']) . ' | Admin Panel' : 'Admin Panel'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <div class="admin-logo">CS</div>
            <div>
                <strong>ClothStore</strong>
                <p>Administrator</p>
            </div>
        </div>

        <nav class="admin-nav">
            <a href="index.php?url=admin" class="admin-nav-link">Панель управління</a>
            <a href="index.php?url=admin-orders" class="admin-nav-link">Замовлення</a>
            <a href="index.php?url=admin-products" class="admin-nav-link">Товари</a>
            <a href="index.php?url=admin-create" class="admin-nav-link">Додати товар</a>
            <a href="index.php?url=admin-users" class="admin-nav-link">Користувачі</a>
            <a href="index.php?url=admin-create-user" class="admin-nav-link">Створити користувача</a>
            <a href="index.php" class="admin-nav-link">На сайт</a>
            <a href="index.php?url=logout" class="admin-nav-link">Вийти</a>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-topbar">
            <div>
                <h1><?= htmlspecialchars($data['title'] ?? 'Адмін панель'); ?></h1>
                <p>Керування магазином, товарами та користувачами</p>
            </div>
        </div>