<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Order.php';

class ProfileController extends Controller
{
    private $userModel;
    private $orderModel;

    public function __construct()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('index.php?url=login');
        }

        $this->userModel = new User();
        $this->orderModel = new Order();
    }

    public function index()
    {
        $userId = (int)$_SESSION['user']['id'];
        $user = $this->userModel->findById($userId);
        $orders = $this->orderModel->getByUserId($userId);

        $this->view('profile/index', [
            'title' => 'Профіль',
            'user' => $user,
            'orders' => $orders,
            'passwordErrors' => [],
            'passwordSuccess' => ''
        ]);
    }

    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=profile');
        }

        $userId = (int)$_SESSION['user']['id'];
        $user = $this->userModel->findById($userId);
        $orders = $this->orderModel->getByUserId($userId);

        $currentPassword = trim($_POST['current_password'] ?? '');
        $newPassword = trim($_POST['new_password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        $errors = [];

        if ($currentPassword === '') {
            $errors['current_password'] = 'Введи поточний пароль';
        } elseif (!password_verify($currentPassword, $user['password'])) {
            $errors['current_password'] = 'Поточний пароль неправильний';
        }

        if ($newPassword === '') {
            $errors['new_password'] = 'Введи новий пароль';
        } elseif (mb_strlen($newPassword) < 6) {
            $errors['new_password'] = 'Новий пароль має містити мінімум 6 символів';
        }

        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Підтверди новий пароль';
        } elseif ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'Паролі не співпадають';
        }

        if (!empty($errors)) {
            $this->view('profile/index', [
                'title' => 'Профіль',
                'user' => $user,
                'orders' => $orders,
                'passwordErrors' => $errors,
                'passwordSuccess' => ''
            ]);
            return;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->userModel->updatePassword($userId, $hashedPassword);

        $user = $this->userModel->findById($userId);

        $this->view('profile/index', [
            'title' => 'Профіль',
            'user' => $user,
            'orders' => $orders,
            'passwordErrors' => [],
            'passwordSuccess' => 'Пароль успішно змінено'
        ]);
    }

    public function order()
    {
        $userId = (int)$_SESSION['user']['id'];
        $orderId = (int)($_GET['id'] ?? 0);

        if ($orderId <= 0) {
            $this->redirect('index.php?url=profile');
        }

        $order = $this->orderModel->getUserOrderWithItems($orderId, $userId);

        if (!$order) {
            $this->redirect('index.php?url=profile');
        }

        $this->view('profile/order', [
            'title' => 'Моє замовлення',
            'order' => $order
        ]);
    }
}