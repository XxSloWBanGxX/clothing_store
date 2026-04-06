<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Product.php';

class NewController extends Controller
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function index()
    {
        $products = $this->productModel->getFeatured(8);

        $this->view('new/index', [
            'title' => 'Новинки',
            'products' => $products
        ]);
    }
}