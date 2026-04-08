<?php

session_start();

require_once __DIR__ . '/../config/config.php';

require_once __DIR__ . '/../app/core/Controller.php';

require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/AboutController.php';
require_once __DIR__ . '/../app/controllers/NewController.php';
require_once __DIR__ . '/../app/controllers/CheckoutController.php';
require_once __DIR__ . '/../app/controllers/AdminOrderController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';
require_once __DIR__ . '/../app/controllers/CatalogController.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/CartController.php';
require_once __DIR__ . '/../app/controllers/FavoritesController.php';

$url = $_GET['url'] ?? 'home';

switch ($url) {
    case 'home':
        $controller = new HomeController();
        $controller->index();
        break;

    case 'about':
        $controller = new AboutController();
        $controller->index();
        break;

    case 'new':
        $controller = new NewController();
        $controller->index();
        break;

    case 'checkout':
        $controller = new CheckoutController();
        $controller->index();
        break;

    case 'checkout-store':
        $controller = new CheckoutController();
        $controller->store();
        break;

    case 'checkout-success':
        $controller = new CheckoutController();
        $controller->success();
        break;

    case 'admin-orders':
        $controller = new AdminOrderController();
        $controller->index();
        break;

    case 'admin-order-show':
        $controller = new AdminOrderController();
        $controller->show();
        break;

    case 'admin-order-update-status':
        $controller = new AdminOrderController();
        $controller->updateStatus();
        break;

    case 'catalog':
        $controller = new CatalogController();
        $controller->index();
        break;

    case 'product':
        $controller = new ProductController();
        $controller->show();
        break;

    case 'add-to-cart':
        $controller = new ProductController();
        $controller->addToCart();
        break;

    case 'add-to-favorites':
        $controller = new ProductController();
        $controller->addToFavorites();
        break;

    case 'cart':
        $controller = new CartController();
        $controller->index();
        break;

    case 'cart-update':
        $controller = new CartController();
        $controller->update();
        break;

    case 'cart-increase':
        $controller = new CartController();
        $controller->increase();
        break;

    case 'cart-decrease':
        $controller = new CartController();
        $controller->decrease();
        break;

    case 'cart-remove':
        $controller = new CartController();
        $controller->remove();
        break;

    case 'cart-clear':
        $controller = new CartController();
        $controller->clear();
        break;

    case 'favorites':
        $controller = new FavoritesController();
        $controller->index();
        break;

    case 'favorites-create-folder':
        $controller = new FavoritesController();
        $controller->createFolder();
        break;

    case 'favorites-remove':
        $controller = new FavoritesController();
        $controller->remove();
        break;

    case 'favorites-clear-folder':
        $controller = new FavoritesController();
        $controller->clearFolder();
        break;

    case 'favorites-delete-folder':
        $controller = new FavoritesController();
        $controller->deleteFolder();
        break;

    case 'admin':
        $controller = new AdminController();
        $controller->dashboard();
        break;

    case 'admin-products':
        $controller = new AdminController();
        $controller->products();
        break;

    case 'admin-create':
        $controller = new AdminController();
        $controller->create();
        break;

    case 'admin-store':
        $controller = new AdminController();
        $controller->store();
        break;

    case 'admin-edit':
        $controller = new AdminController();
        $controller->edit();
        break;

    case 'admin-update':
        $controller = new AdminController();
        $controller->update();
        break;

    case 'admin-delete':
        $controller = new AdminController();
        $controller->delete();
        break;

    case 'admin-users':
        $controller = new AdminController();
        $controller->users();
        break;

    case 'admin-create-user':
        $controller = new AdminController();
        $controller->createUser();
        break;

    case 'admin-store-user':
        $controller = new AdminController();
        $controller->storeUser();
        break;

    case 'admin-delete-user':
        $controller = new AdminController();
        $controller->deleteUser();
        break;

    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;

    case 'login-post':
        $controller = new AuthController();
        $controller->loginPost();
        break;

    case 'register':
        $controller = new AuthController();
        $controller->register();
        break;

    case 'register-post':
        $controller = new AuthController();
        $controller->registerPost();
        break;

    case 'profile':
        $controller = new ProfileController();
        $controller->index();
        break;

    case 'profile-change-password':
        $controller = new ProfileController();
        $controller->changePassword();
        break;

    case 'profile-order':
        $controller = new ProfileController();
        $controller->order();
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    default:
        $controller = new HomeController();
        $controller->index();
        break;
}