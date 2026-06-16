<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\PricingService;
use App\Services\SiteSettings;

class NewController extends Controller
{
    public function __construct(private PricingService $pricing)
    {
    }

    public function index()
    {
        $days = max(1, (int) SiteSettings::get('new_products_days', '30'));
        $since = now()->subDays($days);

        $products = Product::join('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.*', 'categories.name as category_name')
            ->where('products.created_at', '>=', $since)
            ->orderBy('products.id', 'desc')
            ->take(24)
            ->get()
            ->toArray();

        if (empty($products)) {
            $products = Product::join('categories', 'categories.id', '=', 'products.category_id')
                ->select('products.*', 'categories.name as category_name')
                ->orderBy('products.id', 'desc')
                ->take(24)
                ->get()
                ->toArray();
        }

        $products = $this->pricing->applyToMany($products);

        $data = [
            'products' => $products,
            'newDays' => $days,
        ];

        return view('new', compact('data'));
    }
}
