<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SiteSettings
{
    private const CACHE_KEY = 'site_settings_all';

    public static function defaults(): array
    {
        return [
            'brand_name' => 'CLOTHSTORE',
            'brand_lead' => 'CLOTH',
            'brand_accent' => 'STORE',
            'contact_email' => 'info@clothstore.local',
            'contact_phone' => '+380 99 000 00 00',
            'contact_location' => 'Україна',
            'instagram_url' => '',
            'instagram_handle' => '',
            'footer_description' => '',
            'footer_strip_text' => '',
            'footer_strip_link_text' => '',
            'footer_strip_link_url' => '/new',
            'delivery_carriers' => 'Nova Poshta, Ukrposhta, Meest',
            'trust_payment_text' => 'Онлайн або при отриманні',
            'shipping_info' => '',
            'returns_info' => '',
            'hero_badge' => '',
            'hero_title' => '',
            'hero_text' => '',
            'hero_btn1_text' => '',
            'hero_btn1_url' => '/catalog',
            'hero_btn2_text' => '',
            'hero_btn2_url' => '/new',
            'hero_stat1_value' => '',
            'hero_stat1_label' => '',
            'hero_stat2_value' => '',
            'hero_stat2_label' => '',
            'hero_stat3_value' => '',
            'hero_stat3_label' => '',
            'feature1_title' => '',
            'feature1_text' => '',
            'feature2_title' => '',
            'feature2_text' => '',
            'feature3_title' => '',
            'feature3_text' => '',
            'banner_label' => '',
            'banner_title' => '',
            'banner_text' => '',
            'banner_btn_text' => '',
            'banner_btn_url' => '/catalog',
            'new_products_days' => '30',
            'reviews_moderation' => '0',
        ];
    }

    public static function all(): array
    {
        return Cache::remember(self::CACHE_KEY, 300, function () {
            $merged = self::defaults();

            if (! Schema::hasTable('site_settings')) {
                return $merged;
            }

            try {
                $rows = DB::table('site_settings')->get();
                foreach ($rows as $row) {
                    $merged[$row->key] = $row->value ?? '';
                }
            } catch (\Throwable $e) {
                //
            }

            return $merged;
        });
    }

    public static function get(string $key, ?string $default = null): string
    {
        $all = self::all();

        if (array_key_exists($key, $all)) {
            return (string) $all[$key];
        }

        return $default ?? (string) (self::defaults()[$key] ?? '');
    }

    public static function setMany(array $pairs): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        foreach ($pairs as $key => $value) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value === null ? '' : (string) $value]
            );
        }

        self::clearCache();
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function reviewsModerationEnabled(): bool
    {
        return self::get('reviews_moderation') === '1';
    }
}
