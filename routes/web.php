<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\NewController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/catalog', [CatalogController::class, 'index']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::get('/new', [NewController::class, 'index']);
Route::get('/about', [AboutController::class, 'index']);

Route::get('/cart', [CartController::class, 'index']);
Route::post('/cart/add', [CartController::class, 'add']);
Route::post('/cart/update', [CartController::class, 'updateQty']);
Route::post('/cart/remove', [CartController::class, 'remove']);

Route::get('/favorites', [FavoritesController::class, 'index']);
Route::post('/favorites/add', [FavoritesController::class, 'add']);
Route::post('/favorites/create-folder', [FavoritesController::class, 'createFolder']);
Route::post('/favorites/remove', [FavoritesController::class, 'remove']);
Route::post('/favorites/clear-folder', [FavoritesController::class, 'clearFolder']);
Route::post('/favorites/delete-folder', [FavoritesController::class, 'deleteFolder']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile/update', [ProfileController::class, 'updateProfile']);
    Route::post('/profile/password', [ProfileController::class, 'changePassword']);
    Route::get('/profile/order/{id}', [ProfileController::class, 'order']);
    Route::post('/profile/order/{id}/cancel', [ProfileController::class, 'cancelOrder']);

    Route::get('/checkout', [CheckoutController::class, 'index']);
    Route::post('/checkout', [CheckoutController::class, 'store']);
    Route::get('/checkout/success/{id}', [CheckoutController::class, 'success']);

    Route::post('/product/{id}/review', [ReviewController::class, 'store']);
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);

    Route::get('/products', [AdminController::class, 'products']);
    Route::get('/products/create', [AdminController::class, 'create']);
    Route::post('/products', [AdminController::class, 'store']);
    Route::get('/products/{id}/edit', [AdminController::class, 'edit']);
    Route::put('/products/{id}', [AdminController::class, 'update']);
    Route::delete('/products/{id}', [AdminController::class, 'destroy']);

    Route::get('/categories', [AdminController::class, 'categories']);
    Route::post('/categories', [AdminController::class, 'storeCategory']);
    Route::delete('/categories/{id}', [AdminController::class, 'destroyCategory']);

    Route::get('/users', [AdminController::class, 'users']);
    Route::get('/users/create', [AdminController::class, 'createUser']);
    Route::post('/users', [AdminController::class, 'storeUser']);
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser']);

    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
    Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
});
