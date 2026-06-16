<?php

namespace App\Console\Commands;

use App\Services\ProductImportService;
use Illuminate\Console\Command;

class ImportProducts extends Command
{
    protected $signature = 'shop:import-products
                            {csv : Шлях до CSV файлу}
                            {--images= : Шлях до ZIP з фото (необовʼязково)}';

    protected $description = 'Масовий імпорт товарів з CSV (+ опційно ZIP з фото)';

    public function handle(ProductImportService $import): int
    {
        $csv = $this->argument('csv');
        $images = $this->option('images');

        if (! is_file($csv)) {
            $this->error("CSV не знайдено: {$csv}");

            return self::FAILURE;
        }

        if ($images && ! is_file($images)) {
            $this->error("ZIP не знайдено: {$images}");

            return self::FAILURE;
        }

        $this->info('Імпорт запущено…');

        $result = $import->importFromCsv($csv, $images ?: null);

        $this->info("Готово: {$result['created']} товар(ів) створено.");

        if ($result['skipped'] > 0) {
            $this->warn("Пропущено: {$result['skipped']}");
        }

        foreach ($result['errors'] as $error) {
            $this->line("  • {$error}");
        }

        return self::SUCCESS;
    }
}
