<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index()
    {
        // Дістаємо ВСІ товари з бази (а не тільки 4, як на головній)
        $products = Product::join('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.*', 'categories.name as category_name')
            ->orderBy('products.id', 'desc')
            ->get()
            ->toArray();

        // Пакуємо в масив $data
        $data = [
            'products' => $products
        ];

        // Віддаємо сторінку catalog.blade.php
        return view('catalog', compact('data')); 
    }
}