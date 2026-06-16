<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\NewController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\AdminProductImportController;
use App\Http\Controllers\AdminSiteController;
use App\Http\Controllers\AdminPromocodeController;
use App\Http\Controllers\AdminSaleController;
use App\Http\Controllers\AdminNewsletterController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\StockAlertController;
use App\Http\Controllers\ShippingQuoteController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/catalog', [CatalogController::class, 'index']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::get('/new', [NewController::class, 'index']);
Route::get('/sale', [SaleController::class, 'index']);
Route::get('/about', [PageController::class, 'show'])->defaults('slug', 'about');
Route::get('/privacy', [PageController::class, 'show'])->defaults('slug', 'privacy');
Route::get('/cooperation', [PageController::class, 'show'])->defaults('slug', 'cooperation');

Route::post('/support', [SupportController::class, 'store']);
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);

Route::get('/cart', [CartController::class, 'index']);
Route::post('/cart/add', [CartController::class, 'add']);
Route::post('/cart/update', [CartController::class, 'updateQty']);
Route::post('/cart/remove', [CartController::class, 'remove']);

Route::get('/favorites', [FavoritesController::class, 'index']);
Route::post('/favorites/add', [FavoritesController::class, 'add']);
Route::post('/favorites/move', [FavoritesController::class, 'move']);
Route::post('/favorites/create-folder', [FavoritesController::class, 'createFolder']);
Route::post('/favorites/remove', [FavoritesController::class, 'remove']);
Route::post('/favorites/clear-folder', [FavoritesController::class, 'clearFolder']);
Route::post('/favorites/delete-folder', [FavoritesController::class, 'deleteFolder']);
Route::post('/favorites/share', [FavoritesController::class, 'share']);
Route::get('/favorites/share/{token}', [FavoritesController::class, 'showShare']);
Route::post('/favorites/share/{token}/import', [FavoritesController::class, 'importShare']);
Route::post('/favorites/price-alert', [FavoritesController::class, 'priceAlert']);

Route::post('/product/{id}/stock-alert', [StockAlertController::class, 'store']);
Route::get('/api/shipping/quote', [ShippingQuoteController::class, 'quote']);
Route::get('/api/delivery/cities', [DeliveryController::class, 'cities']);
Route::get('/api/delivery/points', [DeliveryController::class, 'points']);

Route::get('/checkout', [CheckoutController::class, 'index']);
Route::post('/checkout', [CheckoutController::class, 'store']);
Route::get('/checkout/success/{id}', [CheckoutController::class, 'success']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile/update', [ProfileController::class, 'updateProfile']);
    Route::post('/profile/delivery', [ProfileController::class, 'updateDelivery']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::post('/profile/password', [ProfileController::class, 'changePassword']);
    Route::get('/profile/order/{id}', [ProfileController::class, 'order']);
    Route::post('/profile/order/{id}/cancel', [ProfileController::class, 'cancelOrder']);
    Route::post('/profile/messages/{id}/reply', [MessageController::class, 'reply']);

    Route::post('/product/{id}/review', [ReviewController::class, 'store']);
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirect']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'callback']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);

    Route::get('/products', [AdminController::class, 'products']);
    Route::get('/products/create', [AdminController::class, 'create']);
    Route::get('/products/import', [AdminProductImportController::class, 'form']);
    Route::get('/products/import/template', [AdminProductImportController::class, 'template']);
    Route::post('/products/import', [AdminProductImportController::class, 'store']);
    Route::post('/products', [AdminController::class, 'store']);
    Route::get('/products/{id}/edit', [AdminController::class, 'edit']);
    Route::put('/products/{id}', [AdminController::class, 'update']);
    Route::delete('/products/{id}', [AdminController::class, 'destroy']);

    Route::get('/categories', [AdminController::class, 'categories']);
    Route::post('/categories', [AdminController::class, 'storeCategory']);
    Route::get('/categories/{id}/edit', [AdminController::class, 'editCategory']);
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory']);
    Route::delete('/categories/{id}', [AdminController::class, 'destroyCategory']);

    Route::get('/settings', [AdminSiteController::class, 'settings']);
    Route::post('/settings', [AdminSiteController::class, 'updateSettings']);
    Route::get('/pages', [AdminSiteController::class, 'pages']);
    Route::get('/pages/{slug}/edit', [AdminSiteController::class, 'editPage']);
    Route::put('/pages/{slug}', [AdminSiteController::class, 'updatePage']);

    Route::get('/promocodes', [AdminPromocodeController::class, 'index']);
    Route::post('/promocodes', [AdminPromocodeController::class, 'store']);
    Route::put('/promocodes/{id}', [AdminPromocodeController::class, 'update']);
    Route::delete('/promocodes/{id}', [AdminPromocodeController::class, 'destroy']);

    Route::get('/sales', [AdminSaleController::class, 'index']);
    Route::post('/sales', [AdminSaleController::class, 'store']);
    Route::put('/sales/{id}', [AdminSaleController::class, 'update']);
    Route::delete('/sales/{id}', [AdminSaleController::class, 'destroy']);

    Route::get('/newsletter', [AdminNewsletterController::class, 'index']);
    Route::post('/newsletter/send', [AdminNewsletterController::class, 'send']);
    Route::post('/newsletter/{id}/unsubscribe', [AdminNewsletterController::class, 'unsubscribe']);
    Route::delete('/newsletter/{id}', [AdminNewsletterController::class, 'destroy']);

    Route::get('/support', [AdminController::class, 'support']);
    Route::post('/support/{id}/resolve', [AdminController::class, 'resolveSupport']);
    Route::delete('/support/{id}', [AdminController::class, 'destroySupport']);

    Route::get('/reviews', [AdminController::class, 'reviews']);
    Route::post('/reviews/{id}/approve', [AdminController::class, 'approveReview']);
    Route::delete('/reviews/{id}', [AdminController::class, 'destroyReview']);

    Route::get('/messages', [AdminMessageController::class, 'index']);
    Route::post('/messages/start', [AdminMessageController::class, 'start']);
    Route::get('/messages/{id}', [AdminMessageController::class, 'show']);
    Route::post('/messages/{id}/reply', [AdminMessageController::class, 'reply']);
    Route::post('/messages/{id}/close', [AdminMessageController::class, 'close']);

    Route::get('/users', [AdminController::class, 'users']);
    Route::get('/users/create', [AdminController::class, 'createUser']);
    Route::post('/users', [AdminController::class, 'storeUser']);
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser']);
    Route::put('/users/{id}', [AdminController::class, 'updateUser']);
    Route::get('/users/{id}', [AdminController::class, 'showUser']);
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser']);

    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
    Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
});
