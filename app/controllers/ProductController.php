<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/ProductImage.php';
require_once __DIR__ . '/../models/ProductSize.php';
require_once __DIR__ . '/../models/ProductColor.php';
require_once __DIR__ . '/../models/Cart.php';

class ProductController extends Controller
{
    private $productModel;
    private $productImageModel;
    private $productSizeModel;
    private $productColorModel;
    private $cartModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->productImageModel = new ProductImage();
        $this->productSizeModel = new ProductSize();
        $this->productColorModel = new ProductColor();
        $this->cartModel = new Cart();

        if (!isset($_SESSION['favorite_folders'])) {
            $_SESSION['favorite_folders'] = [
                'Обране' => []
            ];
        }
    }

    public function show()
    {
        $id = (int)($_GET['id'] ?? 0);
        $slug = trim($_GET['slug'] ?? '');

        $product = null;

        if ($id > 0) {
            $product = $this->productModel->findById($id);
        } elseif ($slug !== '') {
            $product = $this->productModel->findBySlug($slug);
        }

        if (!$product) {
            die('Товар не знайдено');
        }

        $images = $this->productImageModel->getByProductId($product['id']);
        $sizes = $this->productSizeModel->getByProductId($product['id']);
        $colors = $this->productColorModel->getByProductId($product['id']);

        $this->view('product/show', [
            'title' => $product['name'],
            'product' => $product,
            'images' => $images,
            'sizes' => $sizes,
            'colors' => $colors
        ]);
    }

    public function addToCart()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=catalog');
        }

        if (!isset($_SESSION['user'])) {
            $this->redirect('index.php?url=login');
        }

        $userId = (int)$_SESSION['user']['id'];
        $productId = (int)($_POST['product_id'] ?? 0);
        $selectedSize = trim($_POST['selected_size'] ?? '');
        $selectedColorName = trim($_POST['selected_color_name'] ?? '');
        $selectedColorHex = trim($_POST['selected_color_hex'] ?? '');

        if ($selectedSize === '') {
            $selectedSize = null;
        }

        if ($selectedColorName === '') {
            $selectedColorName = null;
        }

        if ($selectedColorHex === '') {
            $selectedColorHex = null;
        }

        if ($productId > 0) {
            $this->cartModel->add(
                $userId,
                $productId,
                1,
                $selectedSize,
                $selectedColorName,
                $selectedColorHex
            );
        }

        $back = $_SERVER['HTTP_REFERER'] ?? 'index.php?url=catalog';
        $this->redirect($back);
    }

    public function addToFavorites()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=catalog');
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $folder = trim($_POST['folder'] ?? 'Обране');

        if ($folder === '') {
            $folder = 'Обране';
        }

        if (!isset($_SESSION['favorite_folders'][$folder])) {
            $_SESSION['favorite_folders'][$folder] = [];
        }

        if ($productId > 0 && !in_array($productId, $_SESSION['favorite_folders'][$folder])) {
            $_SESSION['favorite_folders'][$folder][] = $productId;
        }

        $back = $_SERVER['HTTP_REFERER'] ?? 'index.php?url=catalog';
        $this->redirect($back);
    }
}