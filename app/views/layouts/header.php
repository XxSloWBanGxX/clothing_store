<?php
require_once __DIR__ . '/../../models/Cart.php';

$cartCount = 0;
$favoritesCount = 0;

if (isset($_SESSION['user'])) {
    $cartModel = new Cart();
    $cartCount = $cartModel->countItems((int)$_SESSION['user']['id']);
}

if (!empty($_SESSION['favorite_folders']) && is_array($_SESSION['favorite_folders'])) {
    foreach ($_SESSION['favorite_folders'] as $folderItems) {
        if (is_array($folderItems)) {
            $favoritesCount += count($folderItems);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($data['title']) ? htmlspecialchars($data['title']) . ' | ' . APP_NAME : APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container header-container">
        <a href="index.php" class="logo">CLOTH<span>STORE</span></a>

        <nav class="main-nav">
            <a href="index.php" class="nav-link">Головна</a>
            <a href="index.php?url=catalog" class="nav-link">Каталог</a>
            <a href="#" class="nav-link">Новинки</a>
            <a href="#" class="nav-link">Про нас</a>
        </nav>

        <div class="header-actions">
            <a href="index.php?url=catalog" class="action-link">Пошук</a>

            <?php if (isset($_SESSION['user'])): ?>
                <a href="index.php?url=profile" class="action-link">Профіль</a>
            <?php endif; ?>

            <a href="index.php?url=favorites" class="action-link">
                Обране <span class="cart-count"><?= $favoritesCount; ?></span>
            </a>

            <a href="index.php?url=cart" class="action-link cart-link">
                Кошик <span class="cart-count"><?= $cartCount; ?></span>
            </a>

            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <a href="index.php?url=admin" class="action-link">Адмін</a>
                <?php endif; ?>
                <a href="index.php?url=logout" class="btn btn-outline">Вийти</a>
            <?php else: ?>
                <a href="index.php?url=login" class="btn btn-outline">Увійти</a>
            <?php endif; ?>
        </div>

        <button class="burger" id="burgerBtn" aria-label="Відкрити меню">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <div class="mobile-menu" id="mobileMenu">
        <a href="index.php" class="mobile-link">Головна</a>
        <a href="index.php?url=catalog" class="mobile-link">Каталог</a>
        <a href="index.php?url=favorites" class="mobile-link">Обране</a>
        <a href="index.php?url=cart" class="mobile-link">Кошик</a>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="index.php?url=profile" class="mobile-link">Профіль</a>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <a href="index.php?url=admin" class="mobile-link">Адмін</a>
            <?php endif; ?>
            <a href="index.php?url=logout" class="mobile-link">Вийти</a>
        <?php else: ?>
            <a href="index.php?url=login" class="mobile-link">Увійти</a>
        <?php endif; ?>
    </div>
</header>