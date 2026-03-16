<?php
session_start();

require_once '../config/config.php';

require_once '../app/core/Database.php';
require_once '../app/core/Model.php';
require_once '../app/core/Controller.php';

require_once '../app/models/Product.php';
require_once '../app/models/User.php';

require_once '../app/controllers/HomeController.php';
require_once '../app/controllers/ProductController.php';
require_once '../app/controllers/CartController.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/ProfileController.php';
require_once '../app/controllers/AdminController.php';

$url = $_GET['url'] ?? '';

switch ($url) {
    case '':
        (new HomeController())->index();
        break;

    case 'product':
        (new ProductController())->show();
        break;

    case 'cart':
        (new CartController())->index();
        break;

    case 'cart/add':
        (new CartController())->add();
        break;

    case 'cart/remove':
        (new CartController())->remove();
        break;

    case 'login':
        (new AuthController())->login();
        break;

    case 'register':
        (new AuthController())->register();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    case 'profile':
        (new ProfileController())->index();
        break;

    case 'admin':
        (new AdminController())->dashboard();
        break;

    case 'admin/create':
        (new AdminController())->create();
        break;

    case 'admin/edit':
        (new AdminController())->edit();
        break;

    case 'admin/delete':
        (new AdminController())->delete();
        break;

    default:
        echo "404 - Сторінку не знайдено";
        break;
}