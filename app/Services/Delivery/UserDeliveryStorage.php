<?php

namespace App\Services\Delivery;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserDeliveryStorage
{
    public const CARRIERS = ['nova_poshta', 'ukrposhta', 'meest', 'courier', 'pickup'];

    public static function emptyEntry(): array
    {
        return [
            'city' => '',
            'city_ref' => '',
            'branch' => '',
            'branch_ref' => '',
            'manual' => false,
        ];
    }

    public static function activeCarrier(?object $userRow): string
    {
        $carrier = $userRow->delivery_carrier ?? 'nova_poshta';

        return in_array($carrier, self::CARRIERS, true) ? $carrier : 'nova_poshta';
    }

    public static function all(?object $userRow): array
    {
        $all = [];
        foreach (self::CARRIERS as $carrier) {
            $all[$carrier] = self::emptyEntry();
            if (in_array($carrier, ['courier', 'pickup'], true)) {
                $all[$carrier]['manual'] = true;
            }
        }

        if (! $userRow) {
            return $all;
        }

        if (Schema::hasColumn('users', 'delivery_data') && ! empty($userRow->delivery_data)) {
            $decoded = json_decode($userRow->delivery_data, true);
            if (is_array($decoded)) {
                foreach (self::CARRIERS as $carrier) {
                    if (! isset($decoded[$carrier]) || ! is_array($decoded[$carrier])) {
                        continue;
                    }
                    $all[$carrier] = array_merge(self::emptyEntry(), $decoded[$carrier]);
                }

                return $all;
            }
        }

        $legacyCarrier = $userRow->delivery_carrier ?? null;
        if ($legacyCarrier && in_array($legacyCarrier, self::CARRIERS, true) && ! empty($userRow->delivery_city)) {
            $all[$legacyCarrier] = [
                'city' => (string) ($userRow->delivery_city ?? ''),
                'city_ref' => (string) ($userRow->delivery_city_ref ?? ''),
                'branch' => (string) ($userRow->delivery_branch ?? ''),
                'branch_ref' => (string) ($userRow->delivery_branch_ref ?? ''),
                'manual' => in_array($legacyCarrier, ['courier', 'pickup'], true),
            ];
        }

        return $all;
    }

    public static function forCarrier(?object $userRow, string $carrier): array
    {
        $all = self::all($userRow);

        return $all[$carrier] ?? self::emptyEntry();
    }

    public static function pickerSaved(?object $userRow, ?string $preferredCarrier = null): array
    {
        $carrier = $preferredCarrier ?? self::activeCarrier($userRow);
        if (! in_array($carrier, self::CARRIERS, true)) {
            $carrier = 'nova_poshta';
        }

        $all = self::all($userRow);
        $entry = $all[$carrier];

        return [
            'carrier' => $carrier,
            'city' => $entry['city'],
            'city_ref' => $entry['city_ref'],
            'branch' => $entry['branch'],
            'branch_ref' => $entry['branch_ref'],
            'manual' => (bool) $entry['manual'],
            'all' => $all,
        ];
    }

    public static function save(int $userId, string $carrier, array $entry): void
    {
        if (! in_array($carrier, self::CARRIERS, true)) {
            return;
        }

        $userRow = DB::table('users')->where('id', $userId)->first();
        $all = self::all($userRow);

        $all[$carrier] = [
            'city' => trim((string) ($entry['city'] ?? '')),
            'city_ref' => trim((string) ($entry['city_ref'] ?? '')),
            'branch' => trim((string) ($entry['branch'] ?? '')),
            'branch_ref' => trim((string) ($entry['branch_ref'] ?? '')),
            'manual' => (bool) ($entry['manual'] ?? in_array($carrier, ['courier', 'pickup'], true)),
        ];

        $payload = [
            'delivery_carrier' => $carrier,
            'delivery_city' => $all[$carrier]['city'],
            'delivery_branch' => $all[$carrier]['branch'],
            'delivery_city_ref' => $all[$carrier]['city_ref'] ?: null,
            'delivery_branch_ref' => $all[$carrier]['branch_ref'] ?: null,
        ];

        if (Schema::hasColumn('users', 'delivery_data')) {
            $payload['delivery_data'] = json_encode($all, JSON_UNESCAPED_UNICODE);
        }

        DB::table('users')->where('id', $userId)->update($payload);
    }
}
