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
            ->select('products.*', 'categories.name as category_name', 'categories.slug as category_slug')
            ->findOrFail($id)
            ->toArray();

        // 2. Дістаємо додаткові дані (кольори, розміри, картинки)
        // json_decode(json_encode(...)) — це хитрий трюк, щоб перетворити дані в масиви, як очікує твій старий код
        $images = json_decode(json_encode(DB::table('product_images')->where('product_id', $id)->orderBy('sort_order')->get()), true);
        $sizes = json_decode(json_encode(DB::table('product_sizes')->where('product_id', $id)->orderBy('sort_order')->get()), true);
        $colors = json_decode(json_encode(DB::table('product_colors')->where('product_id', $id)->orderBy('sort_order')->get()), true);

        $reviews = [];
        $avgRating = 0;

        try {
            $reviewsQuery = DB::table('reviews')->where('product_id', $id);

            if (\Illuminate\Support\Facades\Schema::hasColumn('reviews', 'is_approved')) {
                $reviewsQuery->where('is_approved', 1);
            }

            $reviews = json_decode(json_encode(
                $reviewsQuery->orderBy('id', 'desc')->get()
            ), true);

            if (! empty($reviews)) {
                $avgRating = round(array_sum(array_column($reviews, 'rating')) / count($reviews), 1);
            }
        } catch (\Throwable $e) {
            $reviews = [];
        }

        // Схожі товари (та сама категорія, крім поточного)
        $related = json_decode(json_encode(
            DB::table('products')
                ->where('category_id', $product['category_id'])
                ->where('id', '!=', $id)
                ->orderBy('id', 'desc')
                ->take(4)
                ->get()
        ), true);

        // 3. Пакуємо все в один масив $data
        $data = [
            'product' => $product,
            'images'  => $images,
            'sizes'   => $sizes,
            'colors'  => $colors,
            'reviews' => $reviews,
            'avgRating' => $avgRating,
            'related' => $related,
            'favoriteFolders' => array_keys(session('favorite_folders', ['Обране' => []])),
        ];

        return view('product', compact('data'));
    }
}