<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ZipArchive;

class AdminProductImportController extends Controller
{
    private function uploadDir(): string
    {
        $dir = public_path('assets/images/products');

        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }

    private function makeSlug(string $text): string
    {
        $slug = Str::slug($text);

        return $slug !== '' ? $slug : 'product-' . time();
    }

    private function uniqueFileName(string $originalName): string
    {
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $base = pathinfo($originalName, PATHINFO_FILENAME);
        $base = preg_replace('/[^A-Za-z0-9_\-]/u', '_', $base);
        $base = trim(preg_replace('/_+/', '_', $base), '_');

        if ($base === '') {
            $base = 'image';
        }

        if ($ext === '') {
            $ext = 'jpg';
        }

        $finalName = $base . '.' . $ext;
        $counter = 1;

        while (file_exists($this->uploadDir() . DIRECTORY_SEPARATOR . $finalName)) {
            $finalName = $base . '_' . $counter . '.' . $ext;
            $counter++;
        }

        return $finalName;
    }

    private function copyImageFromPath(string $sourcePath, ?string $preferredName = null): ?string
    {
        if (! is_file($sourcePath)) {
            return null;
        }

        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return null;
        }

        $finalName = $this->uniqueFileName($preferredName ?: basename($sourcePath));
        copy($sourcePath, $this->uploadDir() . DIRECTORY_SEPARATOR . $finalName);

        return $finalName;
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

    public function form()
    {
        $categories = DB::table('categories')->orderBy('name')->get();

        return view('admin.products-import', compact('categories'));
    }

    public function template()
    {
        $csv = implode("\n", [
            'name,category_slug,price,old_price,stock,description,sizes,colors,main_image,gallery_images',
            '"Худі Oversize",hoodies,1299,1599,15,"М\'який cotton худі","S|M|L","Чорний|Сірий",hoodie-black.jpg,"hoodie-black-2.jpg|hoodie-black-3.jpg"',
            '"Футболка Basic",t-shirts,599,,20,"Базова футболка","S|M|L|XL","Білий|Чорний",tee-white.jpg,',
        ]);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="products-import-template.csv"',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
            'images_zip' => ['nullable', 'file', 'mimes:zip', 'max:51200'],
        ], [
            'csv_file.required' => 'Завантаж CSV файл',
            'csv_file.mimes' => 'CSV має бути у форматі .csv',
            'images_zip.mimes' => 'Архів має бути ZIP',
        ]);

        $extractDir = storage_path('app/import_' . uniqid());
        mkdir($extractDir, 0777, true);

        if ($request->hasFile('images_zip')) {
            $zip = new ZipArchive();
            if ($zip->open($request->file('images_zip')->getRealPath()) === true) {
                $zip->extractTo($extractDir);
                $zip->close();
            }
        }

        $csvPath = $validated['csv_file']->getRealPath();
        $handle = fopen($csvPath, 'r');

        if (! $handle) {
            return back()->withErrors(['csv_file' => 'Не вдалось прочитати CSV']);
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);

            return back()->withErrors(['csv_file' => 'CSV порожній']);
        }

        $header = array_map(fn ($h) => strtolower(trim((string) $h)), $header);
        $required = ['name', 'category_slug', 'price', 'stock', 'description'];

        foreach ($required as $column) {
            if (! in_array($column, $header, true)) {
                fclose($handle);

                return back()->withErrors(['csv_file' => 'У CSV немає колонки: ' . $column]);
            }
        }

        $categories = DB::table('categories')->pluck('id', 'slug');
        $colorMap = $this->baseColorMap();
        $created = 0;
        $skipped = 0;
        $errors = [];
        $line = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            if (count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $data = [];
            foreach ($header as $index => $key) {
                $data[$key] = trim((string) ($row[$index] ?? ''));
            }

            $name = $data['name'] ?? '';
            $categorySlug = $data['category_slug'] ?? '';

            if ($name === '' || $categorySlug === '') {
                $skipped++;
                $errors[] = "Рядок {$line}: порожня назва або category_slug";
                continue;
            }

            if (! isset($categories[$categorySlug])) {
                $skipped++;
                $errors[] = "Рядок {$line}: категорію «{$categorySlug}» не знайдено";
                continue;
            }

            $slug = $this->makeSlug($name);
            if (DB::table('products')->where('slug', $slug)->exists()) {
                $slug .= '-' . time() . '-' . $line;
            }

            $mainImage = null;
            $mainImageFile = $data['main_image'] ?? '';

            if ($mainImageFile !== '') {
                $source = $this->findImageSource($extractDir, $mainImageFile);
                $mainImage = $source ? $this->copyImageFromPath($source, $mainImageFile) : null;

                if (! $mainImage) {
                    $errors[] = "Рядок {$line}: не знайдено фото «{$mainImageFile}»";
                }
            }

            $productId = DB::table('products')->insertGetId([
                'category_id' => (int) $categories[$categorySlug],
                'name' => $name,
                'slug' => $slug,
                'description' => $data['description'] ?: $name,
                'price' => (float) ($data['price'] ?? 0),
                'old_price' => ($data['old_price'] ?? '') !== '' ? (float) $data['old_price'] : null,
                'image' => $mainImage,
                'stock' => (int) ($data['stock'] ?? 0),
                'is_featured' => 0,
            ]);

            $this->importSizes($productId, $data['sizes'] ?? '');
            $this->importColors($productId, $data['colors'] ?? '', $colorMap);
            $this->importGallery($productId, $data['gallery_images'] ?? '', $extractDir);

            $created++;
        }

        fclose($handle);
        $this->deleteDirectory($extractDir);

        $message = "Імпортовано {$created} товар(ів).";

        if ($skipped > 0) {
            $message .= " Пропущено: {$skipped}.";
        }

        return redirect('/admin/products/import')->with([
            'status' => $message,
            'importErrors' => $errors,
        ]);
    }

    private function findImageSource(string $extractDir, string $filename): ?string
    {
        $filename = basename(str_replace('\\', '/', $filename));
        $direct = $extractDir . DIRECTORY_SEPARATOR . $filename;

        if (is_file($direct)) {
            return $direct;
        }

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($extractDir));
        foreach ($iterator as $file) {
            if ($file->isFile() && strcasecmp($file->getFilename(), $filename) === 0) {
                return $file->getPathname();
            }
        }

        $productsPath = $this->uploadDir() . DIRECTORY_SEPARATOR . $filename;
        if (is_file($productsPath)) {
            return $productsPath;
        }

        return null;
    }

    private function importSizes(int $productId, string $raw): void
    {
        $sizes = array_filter(array_map('trim', explode('|', $raw)));
        $order = 1;

        foreach ($sizes as $size) {
            DB::table('product_sizes')->insert([
                'product_id' => $productId,
                'size_label' => $size,
                'sort_order' => $order++,
            ]);
        }
    }

    private function importColors(int $productId, string $raw, array $colorMap): void
    {
        $colors = array_filter(array_map('trim', explode('|', $raw)));
        $order = 1;

        foreach ($colors as $colorName) {
            if (! isset($colorMap[$colorName])) {
                continue;
            }

            DB::table('product_colors')->insert([
                'product_id' => $productId,
                'color_name' => $colorName,
                'color_hex' => $colorMap[$colorName],
                'sort_order' => $order++,
            ]);
        }
    }

    private function importGallery(int $productId, string $raw, string $extractDir): void
    {
        $files = array_filter(array_map('trim', explode('|', $raw)));
        $order = 1;

        foreach ($files as $fileName) {
            $source = $this->findImageSource($extractDir, $fileName);
            $stored = $source ? $this->copyImageFromPath($source, $fileName) : null;

            if (! $stored) {
                continue;
            }

            DB::table('product_images')->insert([
                'product_id' => $productId,
                'image_path' => $stored,
                'sort_order' => $order++,
            ]);
        }
    }

    private function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $items = scandir($dir) ?: [];

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($dir);
    }
}
