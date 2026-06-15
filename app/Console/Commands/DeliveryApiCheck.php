<?php

namespace App\Console\Commands;

use App\Services\Delivery\DeliveryManager;
use Illuminate\Console\Command;

class DeliveryApiCheck extends Command
{
    protected $signature = 'delivery:check {--city=Київ : Місто для тестового пошуку}';

    protected $description = 'Перевірити підключення API перевізників (НП, Укрпошта, Meest)';

    public function handle(DeliveryManager $delivery): int
    {
        $city = (string) $this->option('city');

        $this->info('Перевірка API доставки');
        $this->line('Тестове місто: ' . $city);
        $this->newLine();

        $carriers = $delivery->carriers();
        $allOk = true;

        foreach (['nova_poshta', 'ukrposhta', 'meest'] as $carrier) {
            $meta = $carriers[$carrier];
            $this->line('── ' . $meta['label'] . ' ──');

            if (! $meta['configured'] && $carrier !== 'nova_poshta') {
                $this->warn('Не налаштовано');
                $allOk = false;
                $this->newLine();
                continue;
            }

            $cities = $delivery->searchCities($carrier, $city);

            if (empty($cities)) {
                $this->error('Міста не знайдено — перевір ключ або мережу');
                $allOk = false;
                $this->newLine();
                continue;
            }

            $this->info('Міста: ' . count($cities) . ' (перше: ' . $cities[0]['name'] . ')');

            $points = $delivery->searchPoints($carrier, $cities[0]['ref'], '');
            if (empty($points)) {
                $this->warn('Відділення не знайдено для обраного міста');
                $allOk = false;
            } else {
                $this->info('Відділення: ' . count($points) . ' (перше: ' . $points[0]['name'] . ')');
            }

            $this->newLine();
        }

        if ($allOk) {
            $this->info('Усі перевірені API працюють. Списки на checkout і в профілі мають підвантажуватись.');

            return self::SUCCESS;
        }

        $this->warn('Деякі API не працюють. Перевір інтернет-зʼєднання або спробуй пізніше.');

        return self::FAILURE;
    }
}
