<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function __construct(private PricingService $pricing)
    {
    }

    public function index()
    {
        $activeSales = $this->pricing->getActiveSales();
        $scheduledSales = $this->pricing->getScheduledSales();
        $paginator = $this->pricing->paginateProductsOnSale(12);
        $productsOnSale = $paginator->items();

        $categories = DB::table('categories')->orderBy('name')->get();

        $favoriteFolders = array_keys(session('favorite_folders', ['Обране' => []]));

        $data = [
            'activeSales' => $activeSales,
            'scheduledSales' => $scheduledSales,
            'products' => $productsOnSale,
            'paginator' => $paginator,
            'categories' => $categories,
            'favoriteFolders' => $favoriteFolders,
            'serverNow' => $this->pricing->now()->toIso8601String(),
        ];

        return view('sale', compact('data'));
    }
}
