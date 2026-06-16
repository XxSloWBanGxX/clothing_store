<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RecentlyViewedService
{
    private const SESSION_KEY = 'recently_viewed';

    private const MAX_ITEMS = 12;

    public static function track(int $productId): void
    {
        if ($productId <= 0) {
            return;
        }

        $ids = session(self::SESSION_KEY, []);
        $ids = array_values(array_filter($ids, fn ($id) => (int) $id !== $productId));
        array_unshift($ids, $productId);
        $ids = array_slice($ids, 0, self::MAX_ITEMS);

        session([self::SESSION_KEY => $ids]);
    }

    public static function getProducts(int $limit = 8, ?int $excludeId = null): array
    {
        $ids = session(self::SESSION_KEY, []);
        if ($excludeId) {
            $ids = array_values(array_filter($ids, fn ($id) => (int) $id !== $excludeId));
        }

        $ids = array_slice($ids, 0, $limit);
        if (empty($ids)) {
            return [];
        }

        $rows = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.*', 'categories.name as category_name')
            ->whereIn('products.id', $ids)
            ->get()
            ->keyBy('id');

        $pricing = app(PricingService::class);
        $products = [];

        foreach ($ids as $id) {
            if (isset($rows[$id])) {
                $products[] = $pricing->applyToProduct((array) $rows[$id]);
            }
        }

        return $products;
    }
}
