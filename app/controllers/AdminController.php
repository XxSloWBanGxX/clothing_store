<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/ProductImage.php';
require_once __DIR__ . '/../models/ProductSize.php';
require_once __DIR__ . '/../models/ProductColor.php';
require_once __DIR__ . '/../models/User.php';

class AdminController extends Controller
{
    private $productModel;
    private $categoryModel;
    private $productImageModel;
    private $productSizeModel;
    private $productColorModel;
    private $userModel;
    private $uploadDir;

    public function __construct()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('index.php?url=login');
        }

        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->productImageModel = new ProductImage();
        $this->productSizeModel = new ProductSize();
        $this->productColorModel = new ProductColor();
        $this->userModel = new User();

        $this->uploadDir = __DIR__ . '/../../public/assets/images/products/';

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    private function makeSlug($text)
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
        $text = trim($text, '-');

        if ($text === '') {
            $text = 'product-' . time();
        }

        return $text;
    }

    private function validateImageExtension($filename)
    {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, $allowed, true);
    }

    private function sanitizeFileName($fileName)
    {
        $base = pathinfo($fileName, PATHINFO_FILENAME);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $base = preg_replace('/[^A-Za-z0-9_\-]/u', '_', $base);
        $base = preg_replace('/_+/', '_', $base);
        $base = trim($base, '_');

        if ($base === '') {
            $base = 'image';
        }

        return $base . '.' . $ext;
    }

    private function getUniqueFileName($originalName)
    {
        $safeName = $this->sanitizeFileName($originalName);
        $base = pathinfo($safeName, PATHINFO_FILENAME);
        $ext = strtolower(pathinfo($safeName, PATHINFO_EXTENSION));

        $finalName = $safeName;
        $counter = 1;

        while (file_exists($this->uploadDir . $finalName)) {
            $finalName = $base . '_' . $counter . '.' . $ext;
            $counter++;
        }

        return $finalName;
    }

    private function uploadSingleImage($file, &$error = null)
    {
        if (!isset($file['name']) || $file['name'] === '') {
            return null;
        }

        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Помилка завантаження головного фото';
            return null;
        }

        if (!$this->validateImageExtension($file['name'])) {
            $error = 'Дозволені тільки jpg, jpeg, png, webp';
            return null;
        }

        $finalFileName = $this->getUniqueFileName($file['name']);
        $destination = $this->uploadDir . $finalFileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $error = 'Не вдалося зберегти головне фото';
            return null;
        }

        return $finalFileName;
    }

    private function uploadMultipleImages($files, &$error = null)
    {
        $uploaded = [];

        if (!isset($files['name']) || !is_array($files['name'])) {
            return $uploaded;
        }

        $count = count($files['name']);

        for ($i = 0; $i < $count; $i++) {
            if ($files['name'][$i] === '') {
                continue;
            }

            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                $error = 'Помилка завантаження одного з фото галереї';
                return [];
            }

            if (!$this->validateImageExtension($files['name'][$i])) {
                $error = 'У галереї дозволені тільки jpg, jpeg, png, webp';
                return [];
            }

            $finalFileName = $this->getUniqueFileName($files['name'][$i]);
            $destination = $this->uploadDir . $finalFileName;

            if (!move_uploaded_file($files['tmp_name'][$i], $destination)) {
                $error = 'Не вдалося зберегти одне з фото галереї';
                return [];
            }

            $uploaded[] = $finalFileName;
        }

        return $uploaded;
    }

    private function deletePhysicalFile($fileName)
    {
        if (!$fileName) {
            return;
        }

        $path = $this->uploadDir . $fileName;

        if (is_file($path)) {
            unlink($path);
        }
    }

    private function parseSizesFromRequest($sizes)
    {
        $result = [];
        $order = 1;

        if (!is_array($sizes)) {
            return $result;
        }

        foreach ($sizes as $size) {
            $size = trim($size);
            if ($size !== '') {
                $result[] = [
                    'value' => $size,
                    'sort_order' => $order++
                ];
            }
        }

        return $result;
    }

    private function getBaseColorMap()
    {
        return [
            'Чорний' => '#111111',
            'Білий' => '#ffffff',
            'Сірий' => '#808080',
            'Бежевий' => '#d6c1a3',
            'Коричневий' => '#6b4423',
            'Синій' => '#2563eb',
            'Блакитний' => '#38bdf8',
            'Зелений' => '#16a34a',
            'Хакі' => '#6b7a3a',
            'Червоний' => '#dc2626',
            'Бордовий' => '#7f1d1d',
            'Рожевий' => '#ec4899',
            'Фіолетовий' => '#7c3aed',
            'Жовтий' => '#facc15',
            'Помаранчевий' => '#f97316',
        ];
    }

    private function parseColorsFromRequest($selectedColors)
    {
        $result = [];
        $order = 1;
        $baseColors = $this->getBaseColorMap();

        if (!is_array($selectedColors)) {
            return $result;
        }

        foreach ($selectedColors as $colorName) {
            $colorName = trim($colorName);
            if ($colorName !== '' && isset($baseColors[$colorName])) {
                $result[] = [
                    'name' => $colorName,
                    'hex' => $baseColors[$colorName],
                    'sort_order' => $order++
                ];
            }
        }

        return $result;
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $products = $this->productModel->countAll();
        $inStock = $this->productModel->countInStock();
        $featured = $this->productModel->countFeatured();
        $categories = count($this->categoryModel->getAll());
        $users = $this->userModel->countAll();
        $supportUsers = $this->userModel->countByRole('support');

        $this->view('admin/dashboard', [
            'title' => 'Адмін панель',
            'stats' => [
                'products' => $products,
                'inStock' => $inStock,
                'featured' => $featured,
                'categories' => $categories,
                'users' => $users,
                'support' => $supportUsers
            ]
        ]);
    }

    public function products()
    {
        $products = $this->productModel->getAllForAdmin();

        $this->view('admin/products', [
            'title' => 'Керування товарами',
            'products' => $products
        ]);
    }

    public function create()
    {
        $categories = $this->categoryModel->getAll();

        $this->view('admin/create', [
            'title' => 'Додати товар',
            'categories' => $categories,
            'errors' => [],
            'old' => [],
            'baseColors' => $this->getBaseColorMap()
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=admin-create');
        }

        $name = trim($_POST['name'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $stock = trim($_POST['stock'] ?? '');
        $sizesInput = $_POST['sizes'] ?? [];
        $selectedColors = $_POST['colors'] ?? [];
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
        $slug = $this->makeSlug($_POST['slug'] ?? $name);

        $errors = [];

        if ($name === '') $errors['name'] = 'Введи назву товару';
        if ($categoryId <= 0) $errors['category_id'] = 'Оберіть категорію';
        if ($description === '') $errors['description'] = 'Введи опис';
        if ($price === '' || !is_numeric($price)) $errors['price'] = 'Введи коректну ціну';
        if ($stock === '' || !is_numeric($stock)) $errors['stock'] = 'Введи кількість';
        if ($this->productModel->slugExists($slug)) $errors['slug'] = 'Такий slug вже існує';

        $mainImageError = null;
        $galleryError = null;

        $mainImage = $this->uploadSingleImage($_FILES['main_image'] ?? [], $mainImageError);
        $galleryImages = $this->uploadMultipleImages($_FILES['gallery_images'] ?? [], $galleryError);

        if ($mainImageError) $errors['main_image'] = $mainImageError;
        if ($galleryError) $errors['gallery_images'] = $galleryError;

        if (!empty($errors)) {
            $categories = $this->categoryModel->getAll();

            $this->view('admin/create', [
                'title' => 'Додати товар',
                'categories' => $categories,
                'errors' => $errors,
                'old' => $_POST,
                'baseColors' => $this->getBaseColorMap()
            ]);
            return;
        }

        $productId = $this->productModel->create([
            'category_id' => $categoryId,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'price' => $price,
            'image' => $mainImage,
            'stock' => (int)$stock,
            'is_featured' => $isFeatured
        ]);

        if (!empty($galleryImages)) {
            $sortOrder = 1;
            foreach ($galleryImages as $galleryImage) {
                $this->productImageModel->create($productId, $galleryImage, $sortOrder++);
            }
        }

        $sizes = $this->parseSizesFromRequest($sizesInput);
        foreach ($sizes as $size) {
            $this->productSizeModel->create($productId, $size['value'], $size['sort_order']);
        }

        $colors = $this->parseColorsFromRequest($selectedColors);
        foreach ($colors as $color) {
            $this->productColorModel->create($productId, $color['name'], $color['hex'], $color['sort_order']);
        }

        $this->redirect('index.php?url=admin-products');
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        $product = $this->productModel->findById($id);

        if (!$product) {
            die('Товар не знайдено');
        }

        $categories = $this->categoryModel->getAll();
        $images = $this->productImageModel->getByProductId($id);
        $sizes = $this->productSizeModel->getByProductId($id);
        $colors = $this->productColorModel->getByProductId($id);

        $selectedSizes = [];
        foreach ($sizes as $size) {
            $selectedSizes[] = $size['size_label'];
        }

        $selectedColors = [];
        foreach ($colors as $color) {
            $selectedColors[] = $color['color_name'];
        }

        $this->view('admin/edit', [
            'title' => 'Редагувати товар',
            'product' => $product,
            'categories' => $categories,
            'images' => $images,
            'selectedSizes' => $selectedSizes,
            'selectedColors' => $selectedColors,
            'errors' => [],
            'baseColors' => $this->getBaseColorMap()
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=admin-products');
        }

        $id = (int)($_POST['id'] ?? 0);
        $product = $this->productModel->findById($id);

        if (!$product) {
            die('Товар не знайдено');
        }

        $name = trim($_POST['name'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $stock = trim($_POST['stock'] ?? '');
        $sizesInput = $_POST['sizes'] ?? [];
        $selectedColors = $_POST['colors'] ?? [];
        $keepGalleryImages = $_POST['keep_gallery_images'] ?? [];
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
        $slug = $this->makeSlug($_POST['slug'] ?? $name);

        $errors = [];

        if ($name === '') $errors['name'] = 'Введи назву товару';
        if ($categoryId <= 0) $errors['category_id'] = 'Оберіть категорію';
        if ($description === '') $errors['description'] = 'Введи опис';
        if ($price === '' || !is_numeric($price)) $errors['price'] = 'Введи коректну ціну';
        if ($stock === '' || !is_numeric($stock)) $errors['stock'] = 'Введи кількість';
        if ($this->productModel->slugExists($slug, $id)) $errors['slug'] = 'Такий slug вже існує';

        $mainImageError = null;
        $galleryError = null;

        $newMainImage = $this->uploadSingleImage($_FILES['main_image'] ?? [], $mainImageError);
        $newGalleryImages = $this->uploadMultipleImages($_FILES['gallery_images'] ?? [], $galleryError);

        if ($mainImageError) $errors['main_image'] = $mainImageError;
        if ($galleryError) $errors['gallery_images'] = $galleryError;

        if (!empty($errors)) {
            $categories = $this->categoryModel->getAll();
            $images = $this->productImageModel->getByProductId($id);

            $this->view('admin/edit', [
                'title' => 'Редагувати товар',
                'product' => array_merge($product, $_POST),
                'categories' => $categories,
                'images' => $images,
                'selectedSizes' => is_array($sizesInput) ? $sizesInput : [],
                'selectedColors' => is_array($selectedColors) ? $selectedColors : [],
                'errors' => $errors,
                'baseColors' => $this->getBaseColorMap()
            ]);
            return;
        }

        $mainImageToSave = $product['image'];

        if ($newMainImage) {
            if (!empty($product['image'])) {
                $this->deletePhysicalFile($product['image']);
            }
            $mainImageToSave = $newMainImage;
        }

        $this->productModel->update($id, [
            'category_id' => $categoryId,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'price' => $price,
            'image' => $mainImageToSave,
            'stock' => (int)$stock,
            'is_featured' => $isFeatured
        ]);

        $existingImages = $this->productImageModel->getByProductId($id);
        $keepGalleryImages = is_array($keepGalleryImages) ? array_map('intval', $keepGalleryImages) : [];

        foreach ($existingImages as $img) {
            if (!in_array((int)$img['id'], $keepGalleryImages, true)) {
                if (!empty($img['image_path'])) {
                    $this->deletePhysicalFile($img['image_path']);
                }
                $this->productImageModel->deleteById((int)$img['id']);
            }
        }

        if (!empty($newGalleryImages)) {
            $sortOrder = $this->productImageModel->getNextSortOrder($id);
            foreach ($newGalleryImages as $galleryImage) {
                $this->productImageModel->create($id, $galleryImage, $sortOrder++);
            }
        }

        $this->productSizeModel->deleteByProductId($id);
        $sizes = $this->parseSizesFromRequest($sizesInput);
        foreach ($sizes as $size) {
            $this->productSizeModel->create($id, $size['value'], $size['sort_order']);
        }

        $this->productColorModel->deleteByProductId($id);
        $colors = $this->parseColorsFromRequest($selectedColors);
        foreach ($colors as $color) {
            $this->productColorModel->create($id, $color['name'], $color['hex'], $color['sort_order']);
        }

        $this->redirect('index.php?url=admin-edit&id=' . $id);
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=admin-products');
        }

        $id = (int)($_POST['id'] ?? 0);
        $product = $this->productModel->findById($id);

        if ($product) {
            if (!empty($product['image'])) {
                $this->deletePhysicalFile($product['image']);
            }

            $images = $this->productImageModel->getByProductId($id);
            foreach ($images as $img) {
                if (!empty($img['image_path'])) {
                    $this->deletePhysicalFile($img['image_path']);
                }
            }

            $this->productModel->delete($id);
        }

        $this->redirect('index.php?url=admin-products');
    }

    public function users()
    {
        $users = $this->userModel->getAll();

        $this->view('admin/users', [
            'title' => 'Користувачі',
            'users' => $users
        ]);
    }

    public function createUser()
    {
        $this->view('admin/create-user', [
            'title' => 'Створити користувача',
            'errors' => [],
            'old' => []
        ]);
    }

    public function storeUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=admin-create-user');
        }

        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = trim($_POST['role'] ?? 'user');
        $isVerified = isset($_POST['is_verified']) ? 1 : 0;

        $errors = [];

        if ($name === '') $errors['name'] = 'Введи імʼя';
        if ($username === '') $errors['username'] = 'Введи username';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Введи коректний email';
        if ($phone === '') $errors['phone'] = 'Введи телефон';
        if ($password === '' || strlen($password) < 6) $errors['password'] = 'Пароль мінімум 6 символів';

        if (!in_array($role, ['user', 'admin', 'support'], true)) {
            $errors['role'] = 'Некоректна роль';
        }

        if ($this->userModel->findByUsername($username)) $errors['username'] = 'Username вже зайнятий';
        if ($this->userModel->findByEmail($email)) $errors['email'] = 'Email вже зайнятий';
        if ($this->userModel->findByPhone($phone)) $errors['phone'] = 'Телефон вже зайнятий';

        if (!empty($errors)) {
            $this->view('admin/create-user', [
                'title' => 'Створити користувача',
                'errors' => $errors,
                'old' => $_POST
            ]);
            return;
        }

        $this->userModel->adminCreate([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role' => $role,
            'is_verified' => $isVerified
        ]);

        $this->redirect('index.php?url=admin-users');
    }

    public function deleteUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=admin-users');
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0 && $id !== (int)$_SESSION['user']['id']) {
            $this->userModel->delete($id);
        }

        $this->redirect('index.php?url=admin-users');
    }
}