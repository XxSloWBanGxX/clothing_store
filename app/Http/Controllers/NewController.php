<?php

namespace App\Http\Controllers;

use App\Models\Product;

class NewController extends Controller
{
    public function index()
    {
        $products = Product::join('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.*', 'categories.name as category_name')
            ->where('products.is_featured', 1)
            ->orderBy('products.id', 'desc')
            ->take(8)
            ->get()
            ->toArray();

        $data = [
            'products' => $products,
        ];

        return view('new', compact('data'));
    }
}
