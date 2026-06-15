<?php

namespace App\Services\Delivery;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NovaPoshtaService
{
    private string $url = 'https://api.novaposhta.ua/v2.0/json/';

    public function searchCities(string $query): array
    {
        $response = $this->request('Address', 'searchSettlements', [
            'CityName' => $query,
            'Limit' => 20,
        ]);

        $addresses = data_get($response, 'data.0.Addresses', []);

        return collect($addresses)->map(fn ($item) => [
            'ref' => $item['DeliveryCity'] ?? $item['Ref'] ?? '',
            'name' => $item['Present'] ?? $item['MainDescription'] ?? '',
            'area' => $item['Area'] ?? '',
        ])->filter(fn ($item) => $item['ref'] && $item['name'])->values()->all();
    }

    public function searchPoints(string $cityRef, string $query = ''): array
    {
        if ($cityRef === '') {
            return [];
        }

        $cacheKey = 'np_points_' . md5($cityRef . '|' . mb_strtolower(trim($query)));

        return Cache::remember($cacheKey, 1800, function () use ($cityRef, $query) {
            $properties = [
                'CityRef' => $cityRef,
                'Limit' => 100,
            ];

            if ($query !== '') {
                $properties['FindByString'] = $query;
            }

            $all = [];
            $page = 1;
            $maxPages = $query !== '' ? 20 : 1;

            do {
                $properties['Page'] = $page;
                $response = $this->request('Address', 'getWarehouses', $properties);
                $batch = $response['data'] ?? [];
                $all = array_merge($all, $batch);
                $total = (int) ($response['info']['totalCount'] ?? count($batch));
                $page++;
            } while (count($batch) === 100 && count($all) < $total && $page <= $maxPages);

            return collect($all)->map(function ($item) {
                $description = $item['Description'] ?? '';
                $type = str_contains(mb_strtolower($description), 'поштомат') ? 'Поштомат' : 'Відділення';

                return [
                    'ref' => $item['Ref'],
                    'name' => $description,
                    'short' => $item['ShortAddress'] ?? $description,
                    'number' => $item['Number'] ?? '',
                    'type' => $type,
                ];
            })->values()->all();
        });
    }

    private function request(string $model, string $method, array $properties): array
    {
        try {
            $response = Http::timeout(25)->post($this->url, [
                'apiKey' => config('services.nova_poshta.api_key', ''),
                'modelName' => $model,
                'calledMethod' => $method,
                'methodProperties' => $properties,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::warning('[NovaPoshta] ' . $e->getMessage());
        }

        return ['success' => false, 'data' => []];
    }
}
