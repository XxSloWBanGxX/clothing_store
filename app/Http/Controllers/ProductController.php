<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Додали для швидких запитів до бази

class ProductController extends Controller
{
    public function show($id)
    {
        // 1. Дістаємо сам товар
        $product = Product::join('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.*', 'categories.name as category_name')
            ->findOrFail($id)
            ->toArray();

        // 2. Дістаємо додаткові дані (кольори, розміри, картинки)
        // json_decode(json_encode(...)) — це хитрий трюк, щоб перетворити дані в масиви, як очікує твій старий код
        $images = json_decode(json_encode(DB::table('product_images')->where('product_id', $id)->orderBy('sort_order')->get()), true);
        $sizes = json_decode(json_encode(DB::table('product_sizes')->where('product_id', $id)->orderBy('sort_order')->get()), true);
        $colors = json_decode(json_encode(DB::table('product_colors')->where('product_id', $id)->orderBy('sort_order')->get()), true);

        // 3. Пакуємо все в один масив $data
        $data = [
            'product' => $product,
            'images'  => $images,
            'sizes'   => $sizes,
            'colors'  => $colors,
        ];

        return view('product', compact('data'));
    }
}