<?php

class HomeController extends Controller
{
    public function index(): void
    {
        $productModel = new Product();
        $products = $productModel->getAll();
        $this->view('home/index', ['products' => $products]);
    }
}