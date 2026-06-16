<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PricingService
{
    private ?Collection $activeSales = null;

    /** @var array<int, array<int>> */
    private array $saleProductIds = [];

    public function now(): Carbon
    {
        return now();
    }

    public function getActiveSales(): Collection
    {
        if ($this->activeSales !== null) {
            return $this->activeSales;
        }

        if (! Schema::hasTable('sales')) {
            return $this->activeSales = collect();
        }

        $now = $this->now();

        $this->activeSales = DB::table('sales')
            ->where('is_active', 1)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->orderByDesc('discount_percent')
            ->get();

        return $this->activeSales;
    }

    public function getScheduledSales(): Collection
    {
        if (! Schema::hasTable('sales')) {
            return collect();
        }

        return DB::table('sales')
            ->where('is_active', 1)
            ->whereNotNull('starts_at')
            ->where('starts_at', '>', $this->now())
            ->orderBy('starts_at')
            ->get();
    }

    public function getBannerSale(): ?object
    {
        return $this->getActiveSales()->first(fn ($sale) => (bool) $sale->show_banner);
    }

    public function saleStatus(object $sale): string
    {
        if (! $sale->is_active) {
            return 'disabled';
        }

        $now = $this->now();

        if ($sale->starts_at && $now->lt(Carbon::parse($sale->starts_at))) {
            return 'scheduled';
        }

        if ($sale->ends_at && $now->gt(Carbon::parse($sale->ends_at))) {
            return 'expired';
        }

        return 'active';
    }

    public function saleStatusLabel(string $status): string
    {
        return match ($status) {
            'active' => 'Активна',
            'scheduled' => 'Запланована',
            'expired' => 'Завершена',
            default => 'Вимкнена',
        };
    }

    /**
     * @param  array<string, mixed>  $product
     * @return array<string, mixed>
     */
    public function applyToProduct(array $product): array
    {
        $basePrice = (float) ($product['price'] ?? 0);
        $manualOld = ! empty($product['old_price']) ? (float) $product['old_price'] : null;

        $sale = $this->getBestSaleForProduct($product);

        if ($sale) {
            $discountPercent = (int) $sale->discount_percent;
            $salePrice = round($basePrice * (1 - $discountPercent / 100), 2);
            $originalPrice = ($manualOld && $manualOld > $basePrice) ? $manualOld : $basePrice;

            return array_merge($product, [
                'price' => $salePrice,
                'old_price' => $originalPrice > $salePrice ? $originalPrice : $basePrice,
                'on_sale' => true,
                'discount_percent' => $discountPercent,
                'sale_title' => $sale->title,
                'sale_id' => (int) $sale->id,
                'base_price' => $basePrice,
            ]);
        }

        $onSale = $manualOld !== null && $manualOld > $basePrice;

        return array_merge($product, [
            'on_sale' => $onSale,
            'discount_percent' => $onSale ? (int) round((1 - $basePrice / $manualOld) * 100) : 0,
            'base_price' => $basePrice,
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $products
     * @return array<int, array<string, mixed>>
     */
    public function applyToMany(array $products): array
    {
        return array_map(fn ($product) => $this->applyToProduct(is_array($product) ? $product : (array) $product), $products);
    }

    /**
     * @param  array<string, mixed>|object  $product
     */
    public function getEffectivePrice(array|object $product): float
    {
        $applied = $this->applyToProduct(is_array($product) ? $product : (array) $product);

        return (float) $applied['price'];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getProductsOnSale(int $limit = 48): array
    {
        if (! Schema::hasTable('products')) {
            return [];
        }

        $products = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.*', 'categories.name as category_name', 'categories.slug as category_slug')
            ->orderByDesc('products.id')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        $onSale = [];

        foreach ($products as $product) {
            $applied = $this->applyToProduct($product);
            if (! empty($applied['on_sale'])) {
                $onSale[] = $applied;
            }
        }

        usort($onSale, fn ($a, $b) => ($b['discount_percent'] ?? 0) <=> ($a['discount_percent'] ?? 0));

        return array_slice($onSale, 0, $limit);
    }

    public function makeSlug(string $title): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'sale';
        }

        $slug = $base;
        $i = 1;

        while (Schema::hasTable('sales') && DB::table('sales')->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    /**
     * @param  array<string, mixed>  $product
     */
    private function getBestSaleForProduct(array $product): ?object
    {
        $categoryId = (int) ($product['category_id'] ?? 0);
        $productId = (int) ($product['id'] ?? 0);

        foreach ($this->getActiveSales() as $sale) {
            if ($this->saleAppliesTo($sale, $productId, $categoryId)) {
                return $sale;
            }
        }

        return null;
    }

    private function saleAppliesTo(object $sale, int $productId, int $categoryId): bool
    {
        return match ($sale->scope) {
            'all' => true,
            'category' => (int) ($sale->category_id ?? 0) === $categoryId,
            'products' => in_array($productId, $this->getSaleProductIds((int) $sale->id), true),
            default => false,
        };
    }

    /** @return array<int> */
    private function getSaleProductIds(int $saleId): array
    {
        if (! isset($this->saleProductIds[$saleId])) {
            if (! Schema::hasTable('sale_products')) {
                $this->saleProductIds[$saleId] = [];
            } else {
                $this->saleProductIds[$saleId] = DB::table('sale_products')
                    ->where('sale_id', $saleId)
                    ->pluck('product_id')
                    ->map(fn ($id) => (int) $id)
                    ->all();
            }
        }

        return $this->saleProductIds[$saleId];
    }
}
