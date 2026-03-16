<?php

class CartController extends Controller
{
    public function index(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        $this->view('cart/index', ['cart' => $cart]);
    }

    public function add(): void
    {
        if (!isset($_GET['id'])) {
            $this->redirect('');
        }

        $productModel = new Product();
        $product = $productModel->getById((int)$_GET['id']);

        if (!$product) {
            $this->redirect('');
        }

        $_SESSION['cart'][] = $product;
        $this->redirect('cart');
    }

    public function remove(): void
    {
        if (isset($_GET['index']) && isset($_SESSION['cart'][$_GET['index']])) {
            unset($_SESSION['cart'][$_GET['index']]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }

        $this->redirect('cart');
    }
}