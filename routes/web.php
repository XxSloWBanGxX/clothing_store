<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoritesController;


Route::get('/', [HomeController::class, 'index']);
Route::get('/catalog', [CatalogController::class, 'index']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::get('/cart', [CartController::class, 'index']);