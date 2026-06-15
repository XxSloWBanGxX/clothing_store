<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
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
            'featured' => DB::table('products')->where('is_featured', 1)->count(),
            'categories' => DB::table('categories')->count(),
            'users' => DB::table('users')->count(),
            'support' => DB::table('users')->where('role', 'support')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function products()
    {
        $products = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.*', 'categories.name as category_name')
            ->orderBy('products.id', 'desc')
            ->get();

        return view('admin.products', compact('products'));
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
            'stock' => ['required', 'integer', 'min:0'],
            'main_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'gallery_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
        ], $this->productMessages());

        $slug = $this->makeSlug($request->input('slug') ?: $validated['name']);

        if (DB::table('products')->where('slug', $slug)->where('id', '!=', $id)->exists()) {
            return back()->withInput()->withErrors(['slug' => 'Такий slug вже існує']);
        }

        $mainImageToSave = $product->image;

        if ($request->hasFile('main_image')) {
            $this->deletePhysicalFile($product->image);
            $mainImageToSave = $this->storeImage($request->file('main_image'));
        }

        DB::table('products')->where('id', $id)->update([
            'category_id' => (int) $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'price' => $validated['price'],
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

    public function users()
    {
        $users = DB::table('users')->orderBy('id', 'desc')->get();

        return view('admin.users', compact('users'));
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

    public function destroyUser(Request $request, $id)
    {
        if ((int) $id !== (int) $request->user()->id) {
            DB::table('users')->where('id', $id)->delete();
        }

        return redirect('/admin/users')->with('status', 'Користувача видалено');
    }
}
