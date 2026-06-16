<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogController extends Controller
{
    public function __construct(private PricingService $pricing)
    {
    }

    public function index(Request $request)
    {
        $category = trim((string) $request->query('category', ''));
        $search = trim((string) $request->query('search', ''));
        $minPrice = trim((string) $request->query('min_price', ''));
        $maxPrice = trim((string) $request->query('max_price', ''));
        $sort = trim((string) $request->query('sort', 'newest'));
        $inStock = $request->query('in_stock') ? 1 : 0;
        $size = trim((string) $request->query('size', ''));
        $color = trim((string) $request->query('color', ''));

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

        if ($size !== '') {
            $query->whereIn('products.id', function ($q) use ($size) {
                $q->select('product_id')->from('product_sizes')->where('size_label', $size);
            });
        }

        if ($color !== '') {
            $query->whereIn('products.id', function ($q) use ($color) {
                $q->select('product_id')->from('product_colors')->where('color_name', $color);
            });
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

        $paginator = $query->paginate(12)->withQueryString();
        $products = json_decode(json_encode($paginator->items()), true);
        $products = $this->pricing->applyToMany($products);

        $categories = json_decode(json_encode(DB::table('categories')->orderBy('name')->get()), true);
        $allSizes = DB::table('product_sizes')->distinct()->orderBy('size_label')->pluck('size_label')->toArray();
        $allColors = DB::table('product_colors')->distinct()->orderBy('color_name')->pluck('color_name')->toArray();

        $favoriteFolders = array_keys(session('favorite_folders', ['Обране' => []]));

        $data = [
            'products' => $products,
            'categories' => $categories,
            'allSizes' => $allSizes,
            'allColors' => $allColors,
            'filters' => [
                'category' => $category,
                'search' => $search,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'sort' => $sort,
                'in_stock' => $inStock,
                'size' => $size,
                'color' => $color,
            ],
            'totalProducts' => $paginator->total(),
            'favoriteFolders' => $favoriteFolders,
            'paginator' => $paginator,
        ];

        return view('catalog', compact('data'));
    }
}
