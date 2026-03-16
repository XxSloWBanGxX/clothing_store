<?php

class AdminController extends Controller
{
    private function checkAdmin(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('');
        }
    }

    public function dashboard(): void
    {
        $this->checkAdmin();

        $productModel = new Product();
        $products = $productModel->getAll();

        $this->view('admin/dashboard', ['products' => $products]);
    }

    public function create(): void
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productModel = new Product();
            $productModel->create($_POST);
            $this->redirect('admin');
        }

        $this->view('admin/create');
    }

    public function edit(): void
    {
        $this->checkAdmin();

        $productModel = new Product();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productModel->update((int)$_GET['id'], $_POST);
            $this->redirect('admin');
        }

        $product = $productModel->getById((int)$_GET['id']);
        $this->view('admin/edit', ['product' => $product]);
    }

    public function delete(): void
    {
        $this->checkAdmin();

        if (isset($_GET['id'])) {
            $productModel = new Product();
            $productModel->delete((int)$_GET['id']);
        }

        $this->redirect('admin');
    }
}