<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Product.php';

class FavoritesController extends Controller
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new Product();

        if (!isset($_SESSION['favorite_folders'])) {
            $_SESSION['favorite_folders'] = [
                'Обране' => []
            ];
        }
    }

    public function index()
    {
        $foldersData = [];

        foreach ($_SESSION['favorite_folders'] as $folderName => $productIds) {
            $items = [];

            foreach ($productIds as $productId) {
                $product = $this->productModel->findById((int)$productId);
                if ($product) {
                    $items[] = $product;
                }
            }

            $foldersData[$folderName] = $items;
        }

        $this->view('favorites/index', [
            'title' => 'Обране',
            'foldersData' => $foldersData
        ]);
    }

    public function createFolder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=favorites');
        }

        $folderName = trim($_POST['folder_name'] ?? '');

        if ($folderName !== '' && !isset($_SESSION['favorite_folders'][$folderName])) {
            $_SESSION['favorite_folders'][$folderName] = [];
        }

        $this->redirect('index.php?url=favorites');
    }

    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=favorites');
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $folder = trim($_POST['folder'] ?? '');

        if ($folder !== '' && isset($_SESSION['favorite_folders'][$folder])) {
            $_SESSION['favorite_folders'][$folder] = array_values(array_filter(
                $_SESSION['favorite_folders'][$folder],
                fn($id) => (int)$id !== $productId
            ));
        }

        $this->redirect('index.php?url=favorites');
    }

    public function clearFolder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=favorites');
        }

        $folder = trim($_POST['folder'] ?? '');

        if ($folder !== '' && isset($_SESSION['favorite_folders'][$folder])) {
            $_SESSION['favorite_folders'][$folder] = [];
        }

        $this->redirect('index.php?url=favorites');
    }

    public function deleteFolder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=favorites');
        }

        $folder = trim($_POST['folder'] ?? '');

        if ($folder !== '' && $folder !== 'Обране' && isset($_SESSION['favorite_folders'][$folder])) {
            unset($_SESSION['favorite_folders'][$folder]);
        }

        $this->redirect('index.php?url=favorites');
    }
}