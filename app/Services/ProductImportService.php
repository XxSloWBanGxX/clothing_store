<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ZipArchive;

class ProductImportService
{
    private ?string $extractDir = null;

    public function uploadDir(): string
    {
        $dir = public_path('assets/images/products');

        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }

    public function importFromCsv(string $csvPath, ?string $imagesZipPath = null): array
    {
        $this->extractDir = storage_path('app/import_' . uniqid());
        mkdir($this->extractDir, 0777, true);

        if ($imagesZipPath && is_file($imagesZipPath)) {
            $zip = new ZipArchive();
            if ($zip->open($imagesZipPath) === true) {
                $zip->extractTo($this->extractDir);
                $zip->close();
            }
        }

        $delimiter = $this->detectDelimiter($csvPath);
        $handle = fopen($csvPath, 'r');
        if (! $handle) {
            $this->cleanup();

            return ['created' => 0, 'skipped' => 0, 'errors' => ['Не вдалось прочитати CSV']];
        }

        $header = fgetcsv($handle, 0, $delimiter);
        if (! $header) {
            fclose($handle);
            $this->cleanup();

            return ['created' => 0, 'skipped' => 0, 'errors' => ['CSV порожній']];
        }

        $header = array_map(fn ($h) => $this->normalizeHeader((string) $h), $header);
        $required = ['name', 'category_slug', 'price', 'stock', 'description'];

        foreach ($required as $column) {
            if (! in_array($column, $header, true)) {
                fclose($handle);
                $this->cleanup();

                return ['created' => 0, 'skipped' => 0, 'errors' => ['У CSV немає колонки: ' . $column]];
            }
        }

        $categories = DB::table('categories')->pluck('id', 'slug');
        $colorMap = $this->baseColorMap();
        $created = 0;
        $skipped = 0;
        $errors = [];
        $line = 1;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
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
                $source = $this->findImageSource($mainImageFile);
                $mainImage = $source ? $this->copyImageFromPath($source, $mainImageFile) : null;

                if (! $mainImage) {
                    $errors[] = "Рядок {$line}: не знайдено фото «{$mainImageFile}»";
                }
            }

            $productData = [
                'category_id' => (int) $categories[$categorySlug],
                'name' => $name,
                'slug' => $slug,
                'description' => $data['description'] ?: $name,
                'price' => (float) ($data['price'] ?? 0),
                'old_price' => ($data['old_price'] ?? '') !== '' ? (float) $data['old_price'] : null,
                'image' => $mainImage,
                'stock' => (int) ($data['stock'] ?? 0),
                'is_featured' => in_array(strtolower($data['is_featured'] ?? ''), ['1', 'yes', 'true', 'так'], true) ? 1 : 0,
            ];

            $productId = DB::table('products')->insertGetId($productData);

            $this->importSizes($productId, $data['sizes'] ?? '');
            $this->importColors($productId, $data['colors'] ?? '', $colorMap);
            $this->importGallery($productId, $data['gallery_images'] ?? '');

            $created++;
        }

        fclose($handle);
        $this->cleanup();

        return compact('created', 'skipped', 'errors');
    }

    public function templateCsv(): string
    {
        $categories = DB::table('categories')->orderBy('name')->pluck('slug', 'name');

        $rows = [
            'name,category_slug,price,old_price,stock,description,sizes,colors,main_image,gallery_images,is_featured',
        ];

        $examples = [
            ['Худі Oversize Black', 'hoodies', '1299', '1599', '15', 'М\'який cotton худі oversize', 'S|M|L|XL', 'Чорний|Сірий', 'hoodie-black.jpg', 'hoodie-black-2.jpg|hoodie-black-3.jpg', '1'],
            ['Футболка Basic White', 't-shirts', '599', '', '25', 'Базова бавовняна футболка', 'S|M|L|XL', 'Білий|Чорний', 'tee-white.jpg', '', '0'],
            ['Куртка Windbreaker', 'jackets', '2499', '2999', '8', 'Легка вітровка на весну', 'S|M|L', 'Синій|Чорний', 'jacket-blue.jpg', 'jacket-blue-2.jpg', '1'],
        ];

        foreach ($examples as $example) {
            if (! isset($categories['Худі']) && $example[1] === 'hoodies') {
                $example[1] = $categories->first() ?: 'hoodies';
            }
            $rows[] = '"' . implode('","', $example) . '"';
        }

        if ($categories->isNotEmpty()) {
            $rows[] = '';
            $rows[] = '# Доступні category_slug: ' . $categories->values()->implode(', ');
        }

        return implode("\n", $rows);
    }

    private function detectDelimiter(string $csvPath): string
    {
        $sample = (string) file_get_contents($csvPath, false, null, 0, 2048);
        $sample = preg_replace('/^\xEF\xBB\xBF/', '', $sample) ?? $sample;
        $firstLine = strtok($sample, "\n") ?: '';
        $commas = substr_count($firstLine, ',');
        $semis = substr_count($firstLine, ';');

        return $semis > $commas ? ';' : ',';
    }

    private function normalizeHeader(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', trim($header)) ?? trim($header);

        return strtolower($header);
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

    private function findImageSource(string $filename): ?string
    {
        $filename = basename(str_replace('\\', '/', $filename));

        if ($this->extractDir) {
            $direct = $this->extractDir . DIRECTORY_SEPARATOR . $filename;
            if (is_file($direct)) {
                return $direct;
            }

            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->extractDir));
            foreach ($iterator as $file) {
                if ($file->isFile() && strcasecmp($file->getFilename(), $filename) === 0) {
                    return $file->getPathname();
                }
            }
        }

        $productsPath = $this->uploadDir() . DIRECTORY_SEPARATOR . $filename;
        if (is_file($productsPath)) {
            return $productsPath;
        }

        return null;
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

    private function importSizes(int $productId, string $raw): void
    {
        foreach (array_filter(array_map('trim', explode('|', $raw))) as $i => $size) {
            DB::table('product_sizes')->insert([
                'product_id' => $productId,
                'size_label' => $size,
                'sort_order' => $i + 1,
            ]);
        }
    }

    private function importColors(int $productId, string $raw, array $colorMap): void
    {
        $order = 1;
        foreach (array_filter(array_map('trim', explode('|', $raw))) as $colorName) {
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

    private function importGallery(int $productId, string $raw): void
    {
        $order = 1;
        foreach (array_filter(array_map('trim', explode('|', $raw))) as $fileName) {
            $source = $this->findImageSource($fileName);
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

    private function cleanup(): void
    {
        if (! $this->extractDir || ! is_dir($this->extractDir)) {
            return;
        }

        $items = scandir($this->extractDir) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $this->extractDir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }
        @rmdir($this->extractDir);
        $this->extractDir = null;
    }

    private function deleteDirectory(string $dir): void
    {
        $items = scandir($dir) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }
        @rmdir($dir);
    }
}
