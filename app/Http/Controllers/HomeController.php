<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::join('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.*', 'categories.name as category_name')
            // ->where('is_featured', 1)  <-- ПРОСТО СТАВИМО ДВА СЛЕШІ ТУТ
            ->orderBy('products.id', 'desc')
            ->take(4)
            ->get()
            ->toArray();

        $data = [
            'featuredProducts' => $featuredProducts
        ];

        return view('home', compact('data')); 
    }
}