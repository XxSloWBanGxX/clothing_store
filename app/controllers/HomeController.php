<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Product.php';

class HomeController extends Controller
{
    public function index()
    {
        $productModel = new Product();
        $featuredProducts = $productModel->getFeatured(4);

        $this->view('home/index', [
            'title' => 'Головна',
            'featuredProducts' => $featuredProducts
        ]);
    }
}