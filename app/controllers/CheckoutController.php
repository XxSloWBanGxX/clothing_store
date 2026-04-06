<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Order.php';

class CheckoutController extends Controller
{
    private $cartModel;
    private $orderModel;

    public function __construct()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('index.php?url=login');
        }

        $this->cartModel = new Cart();
        $this->orderModel = new Order();
    }

    public function index()
    {
        $userId = (int)$_SESSION['user']['id'];
        $items = $this->cartModel->getItemsByUserId($userId);

        if (empty($items)) {
            $this->redirect('index.php?url=cart');
        }

        $total = 0;
        foreach ($items as $item) {
            $total += ((float)$item['price'] * (int)$item['quantity']);
        }

        $user = $_SESSION['user'];

        $this->view('checkout/index', [
            'title' => 'Оформлення замовлення',
            'cartItems' => $items,
            'total' => $total,
            'errors' => [],
            'old' => [
                'full_name' => $user['name'] ?? '',
                'phone' => $user['phone'] ?? '',
                'email' => $user['email'] ?? '',
                'city' => '',
                'address_line' => '',
                'delivery_method' => 'nova_poshta',
                'payment_method' => 'cash_on_delivery',
                'comment' => ''
            ]
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=checkout');
        }

        $userId = (int)$_SESSION['user']['id'];
        $items = $this->cartModel->getItemsByUserId($userId);

        if (empty($items)) {
            $this->redirect('index.php?url=cart');
        }

        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $addressLine = trim($_POST['address_line'] ?? '');
        $deliveryMethod = trim($_POST['delivery_method'] ?? '');
        $paymentMethod = trim($_POST['payment_method'] ?? '');
        $comment = trim($_POST['comment'] ?? '');

        $errors = [];

        if ($fullName === '') $errors['full_name'] = 'Введи імʼя та прізвище';
        if ($phone === '') $errors['phone'] = 'Введи номер телефону';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Введи коректний email';
        if ($city === '') $errors['city'] = 'Введи місто';
        if ($addressLine === '') $errors['address_line'] = 'Введи адресу або відділення';
        if ($deliveryMethod === '') $errors['delivery_method'] = 'Оберіть спосіб доставки';
        if ($paymentMethod === '') $errors['payment_method'] = 'Оберіть спосіб оплати';

        $total = 0;
        foreach ($items as $item) {
            $total += ((float)$item['price'] * (int)$item['quantity']);
        }

        if (!empty($errors)) {
            $this->view('checkout/index', [
                'title' => 'Оформлення замовлення',
                'cartItems' => $items,
                'total' => $total,
                'errors' => $errors,
                'old' => $_POST
            ]);
            return;
        }

        $orderId = $this->orderModel->create([
            'user_id' => $userId,
            'full_name' => $fullName,
            'phone' => $phone,
            'email' => $email,
            'city' => $city,
            'address_line' => $addressLine,
            'delivery_method' => $deliveryMethod,
            'payment_method' => $paymentMethod,
            'comment' => $comment,
            'total_amount' => $total,
            'status' => 'new'
        ]);

        foreach ($items as $item) {
            $this->orderModel->addItem([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'product_name' => $item['name'],
                'product_price' => $item['price'],
                'quantity' => $item['quantity'],
                'selected_size' => $item['selected_size'],
                'selected_color_name' => $item['selected_color_name'],
                'selected_color_hex' => $item['selected_color_hex']
            ]);
        }

        $this->cartModel->clear($userId);

        $this->redirect('index.php?url=checkout-success&id=' . $orderId);
    }

    public function success()
    {
        $userId = (int)$_SESSION['user']['id'];
        $orderId = (int)($_GET['id'] ?? 0);

        $order = $this->orderModel->findById($orderId, $userId);

        if (!$order) {
            $this->redirect('index.php');
        }

        $items = $this->orderModel->getItemsByOrderId($orderId);

        $this->view('checkout/success', [
            'title' => 'Замовлення оформлено',
            'order' => $order,
            'items' => $items
        ]);
    }
}