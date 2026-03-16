<?php

class ProductController extends Controller
{
    public function show(): void
    {
        if (!isset($_GET['id'])) {
            $this->redirect('');
        }

        $productModel = new Product();
        $product = $productModel->getById((int)$_GET['id']);

        if (!$product) {
            $this->redirect('');
        }

        $this->view('home/product', ['product' => $product]);
    }
}