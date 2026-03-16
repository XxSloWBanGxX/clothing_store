<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbanWear Store</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
<header class="header">
    <div class="container nav">
        <a href="<?= BASE_URL ?>" class="logo">UrbanWear</a>

        <nav>
            <a href="<?= BASE_URL ?>">Головна</a>
            <a href="<?= BASE_URL ?>cart">Кошик</a>

            <?php if (isset($_SESSION['user'])): ?>
                <a href="<?= BASE_URL ?>profile">Профіль</a>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <a href="<?= BASE_URL ?>admin">Адмінка</a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>logout">Вийти</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>login">Увійти</a>
                <a href="<?= BASE_URL ?>register">Реєстрація</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container"></main>