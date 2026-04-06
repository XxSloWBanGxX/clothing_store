<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Cart.php';

class CartController extends Controller
{
    private $cartModel;

    public function __construct()
    {
        $this->cartModel = new Cart();

        if (!isset($_SESSION['user'])) {
            $this->redirect('index.php?url=login');
        }
    }

    public function index()
    {
        $userId = (int)$_SESSION['user']['id'];
        $items = $this->cartModel->getItemsByUserId($userId);

        $cartItems = [];
        $total = 0;

        foreach ($items as $item) {
            $subtotal = (float)$item['price'] * (int)$item['quantity'];
            $total += $subtotal;

            $cartItems[] = [
                'cart_item_id' => $item['id'],
                'product' => [
                    'id' => $item['product_id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'image' => $item['image'],
                    'stock' => $item['stock'],
                    'category_name' => $item['category_name']
                ],
                'selected_size' => $item['selected_size'],
                'selected_color_name' => $item['selected_color_name'],
                'selected_color_hex' => $item['selected_color_hex'],
                'quantity' => (int)$item['quantity'],
                'subtotal' => $subtotal
            ];
        }

        $this->view('cart/index', [
            'title' => 'Кошик',
            'cartItems' => $cartItems,
            'total' => $total
        ]);
    }

    public function increase()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=cart');
        }

        $userId = (int)$_SESSION['user']['id'];
        $cartItemId = (int)($_POST['cart_item_id'] ?? 0);

        $item = $this->cartModel->getItemById($cartItemId, $userId);

        if ($item) {
            $newQty = (int)$item['quantity'] + 1;
            $this->cartModel->updateQuantityByCartItemId($cartItemId, $userId, $newQty);
        }

        $this->redirect('index.php?url=cart');
    }

    public function decrease()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=cart');
        }

        $userId = (int)$_SESSION['user']['id'];
        $cartItemId = (int)($_POST['cart_item_id'] ?? 0);

        $item = $this->cartModel->getItemById($cartItemId, $userId);

        if ($item) {
            $newQty = (int)$item['quantity'] - 1;
            $this->cartModel->updateQuantityByCartItemId($cartItemId, $userId, $newQty);
        }

        $this->redirect('index.php?url=cart');
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=cart');
        }

        $userId = (int)$_SESSION['user']['id'];
        $cartItemId = (int)($_POST['cart_item_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($cartItemId > 0) {
            $this->cartModel->updateQuantityByCartItemId($cartItemId, $userId, $quantity);
        }

        $this->redirect('index.php?url=cart');
    }

    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=cart');
        }

        $userId = (int)$_SESSION['user']['id'];
        $cartItemId = (int)($_POST['cart_item_id'] ?? 0);

        if ($cartItemId > 0) {
            $this->cartModel->removeByCartItemId($cartItemId, $userId);
        }

        $this->redirect('index.php?url=cart');
    }

    public function clear()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=cart');
        }

        $userId = (int)$_SESSION['user']['id'];
        $this->cartModel->clear($userId);

        $this->redirect('index.php?url=cart');
    }
}