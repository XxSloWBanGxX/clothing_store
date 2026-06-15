<?php

namespace App\Services\Delivery;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MeestDeliveryService
{
    private string $baseUrl = 'https://publicapi.meest.com';

    public function isConfigured(): bool
    {
        return true;
    }

    public function searchCities(string $query): array
    {
        $response = $this->get('/geo_localities', [
            'search_beginning' => $query,
        ]);

        return collect($response['result'] ?? [])
            ->map(function ($item) {
                $data = $item['data'] ?? $item;

                return [
                    'ref' => $data['city_id'] ?? '',
                    'name' => trim(($data['t_ua'] ?? '') . ' ' . ($data['n_ua'] ?? '')),
                    'area' => trim(($data['reg'] ?? '') . ', ' . ($data['dis'] ?? ''), ', '),
                ];
            })
            ->filter(fn ($item) => $item['ref'] && $item['name'])
            ->unique('ref')
            ->take(20)
            ->values()
            ->all();
    }

    public function searchPoints(string $cityRef, string $query = ''): array
    {
        if ($cityRef === '') {
            return [];
        }

        $cacheKey = 'meest_points_' . md5($cityRef);

        $all = Cache::remember($cacheKey, 3600, function () use ($cityRef) {
            $response = $this->get('/branches', [
                'city' => $cityRef,
                'lang' => 'ua',
            ]);

            return collect($response['result'] ?? [])
                ->map(function ($item) {
                    $street = data_get($item, 'street.ua', '');
                    $number = $item['street_number'] ?? '';
                    $short = trim($street . ($number ? ', ' . $number : ''));
                    $typeLabel = data_get($item, 'type_public.ua', 'Відділення');
                    $isPostomat = str_contains(mb_strtolower($typeLabel), 'поштомат');

                    $label = trim(data_get($item, 'city.ua', '') . ' №' . ($item['num_showcase'] ?? ''));

                    return [
                        'ref' => $item['br_id'] ?? $item['num'] ?? '',
                        'name' => $label !== '' ? $label : $typeLabel,
                        'short' => $short !== '' ? $short : data_get($item, 'city.ua', ''),
                        'number' => (string) ($item['num_showcase'] ?? ''),
                        'type' => $isPostomat ? 'Поштомат' : $typeLabel,
                    ];
                })
                ->filter(fn ($item) => $item['ref'] && $item['name'])
                ->values()
                ->all();
        });

        $items = collect($all);

        if ($query !== '') {
            $needle = mb_strtolower($query);
            $items = $items->filter(function ($item) use ($needle) {
                $hay = mb_strtolower(implode(' ', [$item['name'], $item['short'], $item['number'], $item['type']]));

                return str_contains($hay, $needle);
            });
        }

        return $items->take($query !== '' ? 100 : 150)->values()->all();
    }

    private function get(string $path, array $query = []): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'ClothStore/1.0',
                ])
                ->get($this->baseUrl . $path, $query);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::warning('[MeestPublic] ' . $e->getMessage());
        }

        return [];
    }
}
