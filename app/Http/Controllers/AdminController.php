<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Delivery\UserDeliveryStorage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public static function orderStatusLabels(): array
    {
        return [
            'new' => 'Нове',
            'processing' => 'В обробці',
            'sent' => 'Відправлено',
            'completed' => 'Виконано',
            'cancelled' => 'Скасовано',
        ];
    }

    public static function orderStatusTone(string $status): string
    {
        return match ($status) {
            'new' => 'warning',
            'processing' => 'info',
            'sent' => 'purple',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'neutral',
        };
    }

    public static function deliveryLabels(): array
    {
        return [
            'nova_poshta' => 'Нова Пошта',
            'ukrposhta' => 'Укрпошта',
            'meest' => 'Meest',
            'courier' => 'Курʼєр',
            'pickup' => 'Самовивіз',
        ];
    }

    public static function paymentLabels(): array
    {
        return [
            'cash_on_delivery' => 'Оплата при отриманні',
            'card' => 'Картка онлайн',
        ];
    }

    private function uploadDir(): string
    {
        $dir = public_path('assets/images/products');

        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }

    private function baseColorMap(): array
    {
        return [
            'Чорний' => '#111111',
            'Білий' => '#ffffff',
            'Сірий' => '#808080',
            'Бежевий' => '#d6c1a3',
            'Коричневий' => '#6b4423',
            'Синій' => '#2563eb',
            'Блакитний' => '#38bdf8',
            'Зелений' => '#16a34a',
            'Хакі' => '#6b7a3a',
            'Червоний' => '#dc2626',
            'Бордовий' => '#7f1d1d',
            'Рожевий' => '#ec4899',
            'Фіолетовий' => '#7c3aed',
            'Жовтий' => '#facc15',
            'Помаранчевий' => '#f97316',
        ];
    }

    private function makeSlug(string $text): string
    {
        $slug = Str::slug($text);

        return $slug !== '' ? $slug : 'product-' . time();
    }

    private function getUniqueFileName(UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $base = preg_replace('/[^A-Za-z0-9_\-]/u', '_', $base);
        $base = trim(preg_replace('/_+/', '_', $base), '_');

        if ($base === '') {
            $base = 'image';
        }

        $finalName = $base . '.' . $ext;
        $counter = 1;

        while (file_exists($this->uploadDir() . DIRECTORY_SEPARATOR . $finalName)) {
            $finalName = $base . '_' . $counter . '.' . $ext;
            $counter++;
        }

        return $finalName;
    }

    private function storeImage(?UploadedFile $file): ?string
    {
        if (! $file) {
            return null;
        }

        $name = $this->getUniqueFileName($file);
        $file->move($this->uploadDir(), $name);

        return $name;
    }

    private function deletePhysicalFile(?string $fileName): void
    {
        if (! $fileName) {
            return;
        }

        $path = $this->uploadDir() . DIRECTORY_SEPARATOR . $fileName;

        if (is_file($path)) {
            @unlink($path);
        }
    }

    public function dashboard()
    {
        $stats = [
            'products' => DB::table('products')->count(),
            'inStock' => DB::table('products')->where('stock', '>', 0)->count(),
            'outOfStock' => DB::table('products')->where('stock', '<=', 0)->count(),
            'featured' => DB::table('products')->where('is_featured', 1)->count(),
            'categories' => DB::table('categories')->count(),
            'users' => DB::table('users')->count(),
            'orders' => DB::table('orders')->count(),
            'ordersNew' => DB::table('orders')->where('status', 'new')->count(),
            'reviews' => DB::table('reviews')->count(),
            'support' => DB::table('support_messages')->where('status', '!=', 'resolved')->count(),
        ];

        $stats['revenueTotal'] = (float) DB::table('orders')
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $stats['revenueMonth'] = (float) DB::table('orders')
            ->where('status', '!=', 'cancelled')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $recentOrders = DB::table('orders')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.*', 'users.username as username')
            ->orderByDesc('orders.id')
            ->limit(6)
            ->get();

        $lowStockProducts = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.*', 'categories.name as category_name')
            ->where('products.stock', '<=', 5)
            ->orderBy('products.stock')
            ->limit(8)
            ->get();

        $statusLabels = self::orderStatusLabels();

        $days = 14;
        $salesByDay = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $salesByDay[$date] = 0.0;
        }

        $orderRows = DB::table('orders')
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subDays($days)->startOfDay())
            ->selectRaw('DATE(created_at) as day, SUM(total_amount) as revenue, COUNT(*) as cnt')
            ->groupBy('day')
            ->get();

        foreach ($orderRows as $row) {
            if (isset($salesByDay[$row->day])) {
                $salesByDay[$row->day] = (float) $row->revenue;
            }
        }

        $chartSales = [
            'labels' => array_map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->format('d.m'), array_keys($salesByDay)),
            'values' => array_values($salesByDay),
        ];

        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->where('orders.created_at', '>=', now()->subDays(30))
            ->select('order_items.product_name', DB::raw('SUM(order_items.quantity) as qty_sold'))
            ->groupBy('order_items.product_name')
            ->orderByDesc('qty_sold')
            ->limit(8)
            ->get();

        $chartTop = [
            'labels' => $topProducts->pluck('product_name')->map(fn ($n) => \Illuminate\Support\Str::limit($n, 22))->all(),
            'values' => $topProducts->pluck('qty_sold')->map(fn ($v) => (int) $v)->all(),
        ];

        $sessionsEstimate = max(1, (int) DB::table('users')->where('role', 'user')->count() * 3);
        $ordersMonth = (int) DB::table('orders')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $conversionRate = round(min(100, ($ordersMonth / $sessionsEstimate) * 100), 1);

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'lowStockProducts',
            'statusLabels',
            'chartSales',
            'chartTop',
            'conversionRate',
            'ordersMonth',
        ));
    }

    public function products(Request $request)
    {
        $query = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.*', 'categories.name as category_name');

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('products.name', 'like', '%' . $search . '%')
                    ->orWhere('products.slug', 'like', '%' . $search . '%');
            });
        }

        $categoryId = (int) $request->query('category', 0);
        if ($categoryId > 0) {
            $query->where('products.category_id', $categoryId);
        }

        $stockFilter = (string) $request->query('stock', '');
        if ($stockFilter === 'in') {
            $query->where('products.stock', '>', 0);
        } elseif ($stockFilter === 'out') {
            $query->where('products.stock', '<=', 0);
        } elseif ($stockFilter === 'low') {
            $query->whereBetween('products.stock', [1, 5]);
        } elseif ($stockFilter === 'sale') {
            $query->whereNotNull('products.old_price')
                ->whereColumn('products.old_price', '>', 'products.price');
        }

        $products = $query->orderByDesc('products.id')->get();
        $categories = DB::table('categories')->orderBy('name')->get();

        $filters = [
            'q' => $search,
            'category' => $categoryId ?: '',
            'stock' => $stockFilter,
        ];

        return view('admin.products', compact('products', 'categories', 'filters'));
    }

    public function create()
    {
        $categories = DB::table('categories')->orderBy('name')->get();
        $baseColors = $this->baseColorMap();

        return view('admin.create', compact('categories', 'baseColors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'main_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'gallery_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
        ], $this->productMessages());

        $slug = $this->makeSlug($request->input('slug') ?: $validated['name']);

        if (DB::table('products')->where('slug', $slug)->exists()) {
            return back()->withInput()->withErrors(['slug' => 'Такий slug вже існує']);
        }

        $mainImage = $this->storeImage($request->file('main_image'));

        $productId = DB::table('products')->insertGetId([
            'category_id' => (int) $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'old_price' => $validated['old_price'] ?? null,
            'image' => $mainImage,
            'stock' => (int) $validated['stock'],
            'is_featured' => $request->boolean('is_featured') ? 1 : 0,
        ]);

        $this->syncGalleryNew($productId, $request);
        $this->syncSizes($productId, $request->input('sizes', []));
        $this->syncColors($productId, $request->input('colors', []));

        return redirect('/admin/products')->with('status', 'Товар створено');
    }

    public function edit($id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        if (! $product) {
            abort(404, 'Товар не знайдено');
        }

        $categories = DB::table('categories')->orderBy('name')->get();
        $images = DB::table('product_images')->where('product_id', $id)->orderBy('sort_order')->get();
        $selectedSizes = DB::table('product_sizes')->where('product_id', $id)->orderBy('sort_order')->pluck('size_label')->toArray();
        $selectedColors = DB::table('product_colors')->where('product_id', $id)->orderBy('sort_order')->pluck('color_name')->toArray();
        $baseColors = $this->baseColorMap();

        return view('admin.edit', compact('product', 'categories', 'images', 'selectedSizes', 'selectedColors', 'baseColors'));
    }

    public function update(Request $request, $id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        if (! $product) {
            abort(404, 'Товар не знайдено');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'main_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'gallery_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
        ], $this->productMessages());

        $slug = $this->makeSlug($request->input('slug') ?: $validated['name']);

        if (DB::table('products')->where('slug', $slug)->where('id', '!=', $id)->exists()) {
            return back()->withInput()->withErrors(['slug' => 'Такий slug вже існує']);
        }

        $mainImageToSave = $product->image;
        $mainSource = trim((string) $request->input('main_image_source', 'current'));

        if ($request->hasFile('main_image')) {
            if ($product->image && ! str_starts_with($mainSource, 'gallery:')) {
                $this->deletePhysicalFile($product->image);
            }
            $mainImageToSave = $this->storeImage($request->file('main_image'));
        } elseif (str_starts_with($mainSource, 'gallery:')) {
            $galleryId = (int) substr($mainSource, 8);
            $galleryImage = DB::table('product_images')
                ->where('id', $galleryId)
                ->where('product_id', $id)
                ->first();

            if ($galleryImage) {
                $mainImageToSave = $galleryImage->image_path;
            }
        } elseif ($mainSource === 'remove_main') {
            $this->deletePhysicalFile($product->image);
            $mainImageToSave = null;
        }

        DB::table('products')->where('id', $id)->update([
            'category_id' => (int) $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'old_price' => $validated['old_price'] ?? null,
            'image' => $mainImageToSave,
            'stock' => (int) $validated['stock'],
            'is_featured' => $request->boolean('is_featured') ? 1 : 0,
        ]);

        $keep = array_map('intval', (array) $request->input('keep_gallery_images', []));
        $existing = DB::table('product_images')->where('product_id', $id)->get();

        foreach ($existing as $img) {
            if (! in_array((int) $img->id, $keep, true)) {
                $this->deletePhysicalFile($img->image_path);
                DB::table('product_images')->where('id', $img->id)->delete();
            }
        }

        $this->syncGalleryNew($id, $request);
        $this->applyGalleryOrder($id, (string) $request->input('gallery_order', ''));

        DB::table('product_sizes')->where('product_id', $id)->delete();
        $this->syncSizes($id, $request->input('sizes', []));

        DB::table('product_colors')->where('product_id', $id)->delete();
        $this->syncColors($id, $request->input('colors', []));

        return redirect('/admin/products/' . $id . '/edit')->with('status', 'Товар оновлено');
    }

    public function destroy($id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        if ($product) {
            $this->deletePhysicalFile($product->image);

            $images = DB::table('product_images')->where('product_id', $id)->get();
            foreach ($images as $img) {
                $this->deletePhysicalFile($img->image_path);
            }

            DB::table('product_images')->where('product_id', $id)->delete();
            DB::table('product_sizes')->where('product_id', $id)->delete();
            DB::table('product_colors')->where('product_id', $id)->delete();
            DB::table('products')->where('id', $id)->delete();
        }

        return redirect('/admin/products')->with('status', 'Товар видалено');
    }

    private function syncGalleryNew($productId, Request $request): void
    {
        if (! $request->hasFile('gallery_images')) {
            return;
        }

        $sortOrder = (int) DB::table('product_images')->where('product_id', $productId)->max('sort_order') + 1;

        foreach ($request->file('gallery_images') as $file) {
            if (! $file) {
                continue;
            }

            $name = $this->storeImage($file);

            DB::table('product_images')->insert([
                'product_id' => $productId,
                'image_path' => $name,
                'sort_order' => $sortOrder++,
            ]);
        }
    }

    private function applyGalleryOrder(int $productId, string $raw): void
    {
        $ids = array_values(array_filter(array_map('intval', explode(',', $raw))));

        foreach ($ids as $index => $imageId) {
            DB::table('product_images')
                ->where('id', $imageId)
                ->where('product_id', $productId)
                ->update(['sort_order' => $index + 1]);
        }
    }

    private function syncSizes($productId, $sizes): void
    {
        if (! is_array($sizes)) {
            return;
        }

        $order = 1;

        foreach ($sizes as $size) {
            $size = trim((string) $size);
            if ($size === '') {
                continue;
            }

            DB::table('product_sizes')->insert([
                'product_id' => $productId,
                'size_label' => $size,
                'sort_order' => $order++,
            ]);
        }
    }

    private function syncColors($productId, $colors): void
    {
        if (! is_array($colors)) {
            return;
        }

        $map = $this->baseColorMap();
        $order = 1;

        foreach ($colors as $colorName) {
            $colorName = trim((string) $colorName);
            if ($colorName === '' || ! isset($map[$colorName])) {
                continue;
            }

            DB::table('product_colors')->insert([
                'product_id' => $productId,
                'color_name' => $colorName,
                'color_hex' => $map[$colorName],
                'sort_order' => $order++,
            ]);
        }
    }

    private function productMessages(): array
    {
        return [
            'name.required' => 'Введи назву товару',
            'category_id.required' => 'Оберіть категорію',
            'category_id.exists' => 'Оберіть коректну категорію',
            'description.required' => 'Введи опис',
            'price.required' => 'Введи коректну ціну',
            'price.numeric' => 'Введи коректну ціну',
            'stock.required' => 'Введи кількість',
            'stock.integer' => 'Введи кількість',
            'main_image.image' => 'Головне фото має бути зображенням',
            'main_image.mimes' => 'Дозволені тільки jpg, jpeg, png, webp',
            'gallery_images.*.image' => 'Фото галереї мають бути зображеннями',
            'gallery_images.*.mimes' => 'У галереї дозволені тільки jpg, jpeg, png, webp',
        ];
    }

    public function categories()
    {
        $categories = DB::table('categories')
            ->leftJoin('products', 'products.category_id', '=', 'categories.id')
            ->select('categories.*', DB::raw('COUNT(products.id) as products_count'))
            ->groupBy('categories.id', 'categories.name', 'categories.slug', 'categories.created_at')
            ->orderBy('categories.name')
            ->get();

        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ], [
            'name.required' => 'Введи назву категорії',
        ]);

        $slug = $this->makeSlug($request->input('slug') ?: $validated['name']);

        if (DB::table('categories')->where('slug', $slug)->exists()) {
            return back()->withInput()->withErrors(['slug' => 'Категорія з таким slug вже існує']);
        }

        DB::table('categories')->insert([
            'name' => $validated['name'],
            'slug' => $slug,
            'created_at' => now(),
        ]);

        return redirect('/admin/categories')->with('status', 'Категорію створено');
    }

    public function editCategory($id)
    {
        $category = DB::table('categories')->where('id', $id)->first();

        if (! $category) {
            abort(404);
        }

        return view('admin.category-edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $category = DB::table('categories')->where('id', $id)->first();

        if (! $category) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ], [
            'name.required' => 'Введи назву категорії',
        ]);

        $slug = $this->makeSlug($request->input('slug') ?: $validated['name']);

        if (DB::table('categories')->where('slug', $slug)->where('id', '!=', $id)->exists()) {
            return back()->withInput()->withErrors(['slug' => 'Категорія з таким slug вже існує']);
        }

        DB::table('categories')->where('id', $id)->update([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return redirect('/admin/categories')->with('status', 'Категорію оновлено');
    }

    public function destroyCategory($id)
    {
        $count = DB::table('products')->where('category_id', $id)->count();

        if ($count > 0) {
            return back()->withErrors(['delete' => "Не можна видалити: у категорії {$count} товар(ів)"]);
        }

        DB::table('categories')->where('id', $id)->delete();

        return redirect('/admin/categories')->with('status', 'Категорію видалено');
    }

    public function support()
    {
        $statusFilter = request()->query('status', '');

        $query = DB::table('support_messages')->orderByDesc('id');

        if ($statusFilter === 'new') {
            $query->where('status', '!=', 'resolved');
        } elseif ($statusFilter === 'resolved') {
            $query->where('status', 'resolved');
        }

        $messages = $query->get();

        return view('admin.support', compact('messages', 'statusFilter'));
    }

    public function resolveSupport($id)
    {
        DB::table('support_messages')->where('id', $id)->update(['status' => 'resolved']);

        return redirect('/admin/support')->with('status', 'Звернення позначено як опрацьоване');
    }

    public function destroySupport($id)
    {
        DB::table('support_messages')->where('id', $id)->delete();

        return redirect('/admin/support')->with('status', 'Звернення видалено');
    }

    public function users(Request $request)
    {
        $query = DB::table('users')->orderByDesc('id');

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $roleFilter = trim((string) $request->query('role', ''));
        if ($roleFilter !== '') {
            $query->where('role', $roleFilter);
        }

        $users = $query->get();

        return view('admin.users', compact('users', 'search', 'roleFilter'));
    }

    public function showUser($id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (! $user) {
            abort(404, 'Користувача не знайдено');
        }

        $orders = DB::table('orders')
            ->where('user_id', $id)
            ->orderByDesc('id')
            ->get();

        $orderIds = $orders->pluck('id')->all();
        $orderItems = collect();

        if (! empty($orderIds)) {
            $orderItems = DB::table('order_items')
                ->whereIn('order_id', $orderIds)
                ->get()
                ->groupBy('order_id');
        }

        $reviews = collect();
        if (Schema::hasTable('reviews')) {
            $reviews = DB::table('reviews')
                ->leftJoin('products', 'products.id', '=', 'reviews.product_id')
                ->where('reviews.user_id', $id)
                ->orderByDesc('reviews.id')
                ->select('reviews.*', 'products.name as product_name')
                ->get();
        }

        $promocodes = Schema::hasTable('user_promocodes')
            ? DB::table('user_promocodes')->where('user_id', $id)->orderByDesc('id')->get()
            : collect();

        $bonusHistory = Schema::hasTable('bonus_history')
            ? DB::table('bonus_history')->where('user_id', $id)->orderByDesc('id')->get()
            : collect();

        $conversations = Schema::hasTable('conversations')
            ? DB::table('conversations')->where('user_id', $id)->orderByDesc('last_message_at')->get()
            : collect();

        $deliveryAll = UserDeliveryStorage::all($user);
        $deliveryLabels = self::deliveryLabels();
        $statusLabels = self::orderStatusLabels();
        $paymentLabels = self::paymentLabels();

        $completedOrders = $orders->where('status', '!=', 'cancelled');
        $stats = [
            'orders' => $orders->count(),
            'spent' => (float) $completedOrders->sum('total_amount'),
            'avg' => $completedOrders->count() > 0
                ? (float) $completedOrders->avg('total_amount')
                : 0,
            'reviews' => $reviews->count(),
            'bonus' => (int) ($user->bonus_points ?? 0),
            'last_order_at' => $orders->first()->created_at ?? null,
        ];

        return view('admin.user-show', compact(
            'user',
            'orders',
            'orderItems',
            'reviews',
            'promocodes',
            'bonusHistory',
            'conversations',
            'deliveryAll',
            'deliveryLabels',
            'statusLabels',
            'paymentLabels',
            'stats'
        ));
    }

    public function createUser()
    {
        return view('admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'min:3', 'max:50', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['user', 'admin', 'support'])],
        ], [
            'name.required' => 'Введи імʼя',
            'username.required' => 'Введи username',
            'username.unique' => 'Username вже зайнятий',
            'email.required' => 'Введи коректний email',
            'email.email' => 'Введи коректний email',
            'email.unique' => 'Email вже зайнятий',
            'phone.required' => 'Введи телефон',
            'phone.unique' => 'Телефон вже зайнятий',
            'password.required' => 'Пароль мінімум 6 символів',
            'password.min' => 'Пароль мінімум 6 символів',
            'role.required' => 'Оберіть роль',
            'role.in' => 'Некоректна роль',
        ]);

        DB::table('users')->insert([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_verified' => $request->boolean('is_verified') ? 1 : 0,
        ]);

        return redirect('/admin/users')->with('status', 'Користувача створено');
    }

    public function editUser($id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (! $user) {
            abort(404);
        }

        return view('admin.user-edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (! $user) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'min:3', 'max:50', Rule::unique('users', 'username')->ignore($id)],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($id)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($id)],
            'role' => ['required', Rule::in(['user', 'admin', 'support'])],
            'bonus_points' => ['nullable', 'integer', 'min:0'],
        ]);

        $update = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'is_verified' => $request->boolean('is_verified') ? 1 : 0,
        ];

        if (Schema::hasColumn('users', 'bonus_points')) {
            $update['bonus_points'] = (int) ($validated['bonus_points'] ?? 0);
        }

        if ($request->filled('password')) {
            $request->validate(['password' => ['string', 'min:6']]);
            $update['password'] = Hash::make($request->input('password'));
        }

        DB::table('users')->where('id', $id)->update($update);

        return redirect('/admin/users/' . $id)->with('status', 'Користувача оновлено');
    }

    public function destroyUser(Request $request, $id)
    {
        if ((int) $id !== (int) $request->user()->id) {
            DB::table('users')->where('id', $id)->delete();
        }

        return redirect('/admin/users')->with('status', 'Користувача видалено');
    }

    public function reviews()
    {
        $statusFilter = request()->query('status', '');

        $query = DB::table('reviews')
            ->join('products', 'products.id', '=', 'reviews.product_id')
            ->select(
                'reviews.*',
                'products.name as product_name',
                'products.slug as product_slug'
            )
            ->orderByDesc('reviews.id');

        if ($statusFilter === 'pending' && Schema::hasColumn('reviews', 'is_approved')) {
            $query->where('reviews.is_approved', 0);
        } elseif ($statusFilter === 'approved' && Schema::hasColumn('reviews', 'is_approved')) {
            $query->where('reviews.is_approved', 1);
        }

        $reviews = $query->get();
        $pendingCount = Schema::hasColumn('reviews', 'is_approved')
            ? (int) DB::table('reviews')->where('is_approved', 0)->count()
            : 0;

        return view('admin.reviews', compact('reviews', 'statusFilter', 'pendingCount'));
    }

    public function approveReview($id)
    {
        if (Schema::hasColumn('reviews', 'is_approved')) {
            DB::table('reviews')->where('id', $id)->update(['is_approved' => 1]);
        }

        return redirect('/admin/reviews')->with('status', 'Відгук опубліковано');
    }

    public function destroyReview($id)
    {
        DB::table('reviews')->where('id', $id)->delete();

        return redirect('/admin/reviews')->with('status', 'Відгук видалено');
    }
}
