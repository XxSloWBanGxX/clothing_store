<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

class CatalogController extends Controller
{
    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();

        if (!isset($_SESSION['favorite_folders'])) {
            $_SESSION['favorite_folders'] = [
                'Обране' => []
            ];
        }
    }

    public function index()
    {
        $category = trim($_GET['category'] ?? '');
        $search = trim($_GET['search'] ?? '');
        $minPrice = trim($_GET['min_price'] ?? '');
        $maxPrice = trim($_GET['max_price'] ?? '');
        $sort = trim($_GET['sort'] ?? 'newest');
        $inStock = isset($_GET['in_stock']) ? 1 : 0;
        $page = max(1, (int)($_GET['page'] ?? 1));

        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'category' => $category,
            'search' => $search,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'sort' => $sort,
            'in_stock' => $inStock
        ];

        $totalProducts = $this->productModel->countFiltered($filters);
        $totalPages = max(1, (int)ceil($totalProducts / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $perPage;
        }

        $products = $this->productModel->getFiltered($filters, $perPage, $offset);
        $categories = $this->categoryModel->getAll();
        $favoriteFolders = array_keys($_SESSION['favorite_folders']);

        $this->view('catalog/index', [
            'title' => 'Каталог',
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'favoriteFolders' => $favoriteFolders
        ]);
    }
}