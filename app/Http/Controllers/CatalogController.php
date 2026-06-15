<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $category = trim((string) $request->query('category', ''));
        $search = trim((string) $request->query('search', ''));
        $minPrice = trim((string) $request->query('min_price', ''));
        $maxPrice = trim((string) $request->query('max_price', ''));
        $sort = trim((string) $request->query('sort', 'newest'));
        $inStock = $request->query('in_stock') ? 1 : 0;

        $query = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.*', 'categories.name as category_name', 'categories.slug as category_slug');

        if ($category !== '') {
            $query->where('categories.slug', $category);
        }

        if ($search !== '') {
            $query->where('products.name', 'like', '%' . $search . '%');
        }

        if ($minPrice !== '' && is_numeric($minPrice)) {
            $query->where('products.price', '>=', (float) $minPrice);
        }

        if ($maxPrice !== '' && is_numeric($maxPrice)) {
            $query->where('products.price', '<=', (float) $maxPrice);
        }

        if ($inStock) {
            $query->where('products.stock', '>', 0);
        }

        switch ($sort) {
            case 'price_asc':
                $query->orderBy('products.price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('products.price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('products.name', 'asc');
                break;
            default:
                $query->orderBy('products.id', 'desc');
                break;
        }

        $products = json_decode(json_encode($query->get()), true);
        $categories = json_decode(json_encode(DB::table('categories')->orderBy('name')->get()), true);

        $favoriteFolders = array_keys(session('favorite_folders', ['Обране' => []]));

        $data = [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'category' => $category,
                'search' => $search,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'sort' => $sort,
                'in_stock' => $inStock,
            ],
            'totalProducts' => count($products),
            'favoriteFolders' => $favoriteFolders,
        ];

        return view('catalog', compact('data'));
    }
}
