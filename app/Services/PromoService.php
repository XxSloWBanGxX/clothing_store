<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PromoService
{
    public function validate(string $code, int $userId, float $subtotal): array
    {
        $code = strtoupper(trim($code));

        if ($code === '') {
            return ['valid' => false, 'message' => 'Введи промокод'];
        }

        if (Schema::hasTable('promocodes')) {
            $global = DB::table('promocodes')
                ->where('code', $code)
                ->where('is_active', 1)
                ->first();

            if ($global) {
                $error = $this->globalError($global, $subtotal);
                if ($error) {
                    return ['valid' => false, 'message' => $error];
                }

                $discount = $this->calcDiscount($subtotal, (int) $global->discount_percent);

                return [
                    'valid' => true,
                    'code' => $code,
                    'source' => 'global',
                    'source_id' => (int) $global->id,
                    'discount_percent' => (int) $global->discount_percent,
                    'discount_amount' => $discount,
                    'title' => $global->title,
                ];
            }
        }

        if (Schema::hasTable('user_promocodes')) {
            $personal = DB::table('user_promocodes')
                ->where('user_id', $userId)
                ->where('code', $code)
                ->whereNull('used_at')
                ->first();

            if ($personal) {
                if ($personal->expires_at && now()->greaterThan($personal->expires_at)) {
                    return ['valid' => false, 'message' => 'Промокод прострочений'];
                }

                $discount = $this->calcDiscount($subtotal, (int) $personal->discount_percent);

                return [
                    'valid' => true,
                    'code' => $code,
                    'source' => 'user',
                    'source_id' => (int) $personal->id,
                    'discount_percent' => (int) $personal->discount_percent,
                    'discount_amount' => $discount,
                    'title' => $personal->title,
                ];
            }
        }

        return ['valid' => false, 'message' => 'Промокод не знайдено або вже використаний'];
    }

    public function markUsed(array $promo): void
    {
        if (($promo['source'] ?? '') === 'global') {
            DB::table('promocodes')->where('id', $promo['source_id'])->increment('uses_count');
        }

        if (($promo['source'] ?? '') === 'user') {
            DB::table('user_promocodes')
                ->where('id', $promo['source_id'])
                ->update(['used_at' => now()]);
        }
    }

    private function globalError(object $promo, float $subtotal): ?string
    {
        if ($promo->starts_at && now()->lt($promo->starts_at)) {
            return 'Промокод ще не активний';
        }

        if ($promo->expires_at && now()->greaterThan($promo->expires_at)) {
            return 'Промокод прострочений';
        }

        if ($promo->max_uses !== null && (int) $promo->uses_count >= (int) $promo->max_uses) {
            return 'Промокод більше не діє';
        }

        if ($promo->min_order_amount !== null && $subtotal < (float) $promo->min_order_amount) {
            $min = number_format((float) $promo->min_order_amount, 0, '.', ' ');

            return "Мінімальна сума замовлення для цього промокоду — {$min} грн";
        }

        return null;
    }

    private function calcDiscount(float $subtotal, int $percent): float
    {
        return round($subtotal * ($percent / 100), 2);
    }

    public function promocodeStatus(object $promo): string
    {
        if (! $promo->is_active) {
            return 'disabled';
        }

        $now = now();

        if (! empty($promo->starts_at) && $now->lt($promo->starts_at)) {
            return 'scheduled';
        }

        if (! empty($promo->expires_at) && $now->gt($promo->expires_at)) {
            return 'expired';
        }

        if ($promo->max_uses !== null && (int) $promo->uses_count >= (int) $promo->max_uses) {
            return 'expired';
        }

        return 'active';
    }

    public function promocodeStatusLabel(string $status): string
    {
        return match ($status) {
            'active' => 'Активний',
            'scheduled' => 'Запланований',
            'expired' => 'Завершений',
            default => 'Вимкнено',
        };
    }
}
