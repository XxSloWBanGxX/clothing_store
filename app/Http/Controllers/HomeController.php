<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\PricingService;
use App\Services\RecentlyViewedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function __construct(private PricingService $pricing)
    {
    }

    public function index()
    {
        $featuredProducts = Product::join('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.*', 'categories.name as category_name')
             ->where('is_featured', 1)
            ->orderBy('products.id', 'desc')
            ->take(4)
            ->get()
            ->toArray();

        // Якщо популярних немає — показуємо просто останні
        if (empty($featuredProducts)) {
            $featuredProducts = Product::join('categories', 'categories.id', '=', 'products.category_id')
                ->select('products.*', 'categories.name as category_name')
                ->orderBy('products.id', 'desc')
                ->take(4)
                ->get()
                ->toArray();
        }

        // Вітрина категорій з прикладом фото та кількістю товарів
        $categories = DB::table('categories')
            ->orderBy('name')
            ->get()
            ->map(function ($cat) {
                $cat->products_count = DB::table('products')->where('category_id', $cat->id)->count();
                $cat->sample_image = DB::table('products')
                    ->where('category_id', $cat->id)
                    ->whereNotNull('image')
                    ->value('image');

                return $cat;
            });

        $hasNewFlag = Schema::hasColumn('products', 'is_new')
            && DB::table('products')->where('is_new', 1)->exists();

        $newProducts = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.*', 'categories.name as category_name')
            ->when($hasNewFlag, fn ($q) => $q->where('products.is_new', 1))
            ->orderByDesc('products.id')
            ->take(8)
            ->get();
        $newProducts = json_decode(json_encode($newProducts), true);
        $newProducts = $this->pricing->applyToMany($newProducts);

        $featuredProducts = $this->pricing->applyToMany($featuredProducts);

        $recentlyViewed = RecentlyViewedService::getProducts(8);

        $data = [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'newProducts' => $newProducts,
            'recentlyViewed' => $recentlyViewed,
        ];

        return view('home', compact('data')); 
    }
}