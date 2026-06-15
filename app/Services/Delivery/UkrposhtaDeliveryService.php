<?php

namespace App\Services\Delivery;

use App\Services\Delivery\Concerns\ParsesXmlEntries;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UkrposhtaDeliveryService
{
    use ParsesXmlEntries;

    private string $baseUrl = 'https://www.ukrposhta.ua/address-classifier';

    public function isConfigured(): bool
    {
        return true;
    }

    public function searchCities(string $query): array
    {
        $response = $this->get('/get_city_by_region_id_and_district_id_and_city_ua', [
            'city_ua' => $query,
        ]);

        return collect($this->parseXmlEntries($response))
            ->map(fn ($item) => [
                'ref' => $item['CITY_ID'] ?? '',
                'name' => trim(($item['SHORTCITYTYPE_UA'] ?? $item['CITYTYPE_UA'] ?? '') . ' ' . ($item['CITY_UA'] ?? '')),
                'area' => trim(($item['REGION_UA'] ?? '') . ', ' . ($item['DISTRICT_UA'] ?? ''), ', '),
                'population' => (int) ($item['POPULATION'] ?? 0),
                'is_center' => (int) ($item['IS_DISTRICTCENTER'] ?? 0),
            ])
            ->filter(fn ($item) => $item['ref'] && $item['name'])
            ->sortByDesc(fn ($item) => [$item['is_center'], $item['population']])
            ->unique('ref')
            ->take(20)
            ->map(fn ($item) => [
                'ref' => $item['ref'],
                'name' => $item['name'],
                'area' => $item['area'],
            ])
            ->values()
            ->all();
    }

    public function searchPoints(string $cityRef, string $query = ''): array
    {
        if ($cityRef === '') {
            return [];
        }

        $cacheKey = 'ukrposhta_points_' . md5($cityRef);

        $all = Cache::remember($cacheKey, 3600, function () use ($cityRef) {
            return collect($this->parseXmlEntries($this->get('/get_postoffices_by_postcode_cityid_cityvpzid', [
                'city_id' => $cityRef,
            ])))
                ->filter(fn ($item) => ($item['LOCK_CODE'] ?? '0') === '0')
                ->map(function ($item) {
                    $typeLong = $item['TYPE_LONG'] ?? 'Відділення';
                    $isPostomat = str_contains(mb_strtolower($typeLong), 'поштомат')
                        || ($item['TYPE_ACRONYM'] ?? '') === 'П-т'
                        || ($item['POSTTERMINAL'] ?? '0') === '1';

                    return [
                        'ref' => $item['POSTOFFICE_ID'] ?? $item['POSTINDEX'] ?? '',
                        'name' => $item['POSTOFFICE_UA'] ?? $item['PO_SHORT'] ?? 'Відділення',
                        'short' => $item['STREET_UA_VPZ'] ?? $item['ADDRESS'] ?? '',
                        'number' => $item['POSTINDEX'] ?? $item['POSTCODE'] ?? '',
                        'type' => $isPostomat ? 'Поштомат' : $typeLong,
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

    private function get(string $path, array $query): string
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/xml, text/xml, */*',
                    'User-Agent' => 'ClothStore/1.0',
                ])
                ->get($this->baseUrl . $path, $query);

            if ($response->successful()) {
                return $response->body();
            }
        } catch (\Throwable $e) {
            Log::warning('[Ukrposhta] ' . $e->getMessage());
        }

        return '';
    }
}
