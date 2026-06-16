<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminSaleController extends Controller
{
    public function __construct(private PricingService $pricing)
    {
    }

    public function index(Request $request)
    {
        $sales = Schema::hasTable('sales')
            ? DB::table('sales')->orderByDesc('id')->get()
            : collect();

        $categories = DB::table('categories')->orderBy('name')->get();
        $products = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select(
                'products.id',
                'products.name',
                'products.price',
                'products.category_id',
                'categories.name as category_name'
            )
            ->orderBy('products.name')
            ->get();
        $categoryNames = $categories->pluck('name', 'id');

        $saleProducts = [];
        if (Schema::hasTable('sale_products') && $sales->isNotEmpty()) {
            $rows = DB::table('sale_products')
                ->whereIn('sale_id', $sales->pluck('id'))
                ->get();

            foreach ($rows as $row) {
                $saleProducts[(int) $row->sale_id][] = (int) $row->product_id;
            }
        }

        $activeTab = $request->query('tab', 'create');
        if (! in_array($activeTab, ['create', 'list'], true)) {
            $activeTab = 'create';
        }

        $filter = $request->query('filter', 'all');
        $stats = [
            'total' => $sales->count(),
            'active' => 0,
            'scheduled' => 0,
            'expired' => 0,
        ];

        $filtered = $sales->filter(function ($sale) use (&$stats, $filter) {
            $status = $this->pricing->saleStatus($sale);
            if ($status === 'active') {
                $stats['active']++;
            } elseif ($status === 'scheduled') {
                $stats['scheduled']++;
            } elseif ($status === 'expired') {
                $stats['expired']++;
            }

            if ($filter === 'all') {
                return true;
            }

            return $status === $filter || ($filter === 'disabled' && $status === 'disabled');
        });

        $serverNow = $this->pricing->now();

        return view('admin.sales', compact(
            'sales',
            'filtered',
            'stats',
            'categories',
            'categoryNames',
            'products',
            'saleProducts',
            'activeTab',
            'filter',
            'serverNow'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateSale($request);

        $slug = $this->pricing->makeSlug($validated['title']);

        $saleId = DB::table('sales')->insertGetId([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'discount_percent' => (int) $validated['discount_percent'],
            'scope' => $validated['scope'],
            'category_id' => $validated['scope'] === 'category' ? (int) $validated['category_id'] : null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => $request->boolean('is_active', true) ? 1 : 0,
            'show_banner' => $request->boolean('show_banner') ? 1 : 0,
            'banner_text' => $validated['banner_text'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->syncProducts($saleId, $validated['scope'], $request->input('product_ids', []));

        return redirect('/admin/sales?tab=list')->with('status', 'Акцію створено');
    }

    public function update(Request $request, $id)
    {
        $sale = DB::table('sales')->where('id', $id)->first();

        if (! $sale) {
            abort(404);
        }

        $validated = $this->validateSale($request, (int) $id);

        DB::table('sales')->where('id', $id)->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'discount_percent' => (int) $validated['discount_percent'],
            'scope' => $validated['scope'],
            'category_id' => $validated['scope'] === 'category' ? (int) $validated['category_id'] : null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => $request->boolean('is_active') ? 1 : 0,
            'show_banner' => $request->boolean('show_banner') ? 1 : 0,
            'banner_text' => $validated['banner_text'] ?? null,
            'updated_at' => now(),
        ]);

        $this->syncProducts((int) $id, $validated['scope'], $request->input('product_ids', []));

        return redirect('/admin/sales?tab=list&edit=' . $id)->with('status', 'Акцію оновлено');
    }

    public function destroy($id)
    {
        if (Schema::hasTable('sale_products')) {
            DB::table('sale_products')->where('sale_id', $id)->delete();
        }

        DB::table('sales')->where('id', $id)->delete();

        return redirect('/admin/sales?tab=list')->with('status', 'Акцію видалено');
    }

    private function validateSale(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'discount_percent' => ['required', 'integer', 'min:1', 'max:90'],
            'scope' => ['required', Rule::in(['all', 'category', 'products'])],
            'category_id' => ['nullable', 'integer', 'exists:categories,id', Rule::requiredIf(fn () => $request->input('scope') === 'category')],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'banner_text' => ['nullable', 'string', 'max:300'],
            'product_ids' => ['nullable', 'array', Rule::requiredIf(fn () => $request->input('scope') === 'products')],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ];

        return $request->validate($rules, [
            'title.required' => 'Введи назву акції',
            'discount_percent.required' => 'Вкажи відсоток знижки',
            'ends_at.after_or_equal' => 'Дата завершення має бути після дати початку',
            'category_id.required' => 'Обери категорію для акції',
            'product_ids.required' => 'Обери хоча б один товар',
        ]);
    }

    private function syncProducts(int $saleId, string $scope, array $productIds): void
    {
        if (! Schema::hasTable('sale_products')) {
            return;
        }

        DB::table('sale_products')->where('sale_id', $saleId)->delete();

        if ($scope !== 'products') {
            return;
        }

        foreach (array_unique(array_map('intval', $productIds)) as $productId) {
            DB::table('sale_products')->insert([
                'sale_id' => $saleId,
                'product_id' => $productId,
            ]);
        }
    }
}
