<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Order.php';

class AdminOrderController extends Controller
{
    private $orderModel;

    public function __construct()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('index.php?url=login');
        }

        $this->orderModel = new Order();
    }

    public function index()
    {
        $orders = $this->orderModel->getAllOrders();

        $this->view('admin/orders', [
            'title' => 'Замовлення',
            'orders' => $orders
        ]);
    }

    public function show()
    {
        $id = (int)($_GET['id'] ?? 0);
        $order = $this->orderModel->getOrderWithItems($id);

        if (!$order) {
            die('Замовлення не знайдено');
        }

        $this->view('admin/order-show', [
            'title' => 'Перегляд замовлення',
            'order' => $order
        ]);
    }

    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=admin-orders');
        }

        $id = (int)($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? 'new');

        $allowed = ['new', 'processing', 'sent', 'completed', 'cancelled'];

        if ($id > 0 && in_array($status, $allowed, true)) {
            $this->orderModel->updateStatus($id, $status);
        }

        $this->redirect('index.php?url=admin-order-show&id=' . $id);
    }
}