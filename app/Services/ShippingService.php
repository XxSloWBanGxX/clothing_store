<?php

namespace App\Services;

class ShippingService
{
    public function calculate(string $carrier, float $subtotal, int $itemCount = 1): array
    {
        $itemCount = max(1, $itemCount);
        $freeFrom = 2000.0;

        if ($subtotal >= $freeFrom && in_array($carrier, ['nova_poshta', 'ukrposhta', 'meest'], true)) {
            return [
                'amount' => 0.0,
                'label' => 'Безкоштовно',
                'note' => 'Доставка безкоштовна від ' . number_format($freeFrom, 0, '.', ' ') . ' грн',
            ];
        }

        $base = match ($carrier) {
            'nova_poshta' => 79.0,
            'ukrposhta' => 55.0,
            'meest' => 69.0,
            'courier' => 120.0,
            'pickup' => 0.0,
            default => 79.0,
        };

        $extra = max(0, $itemCount - 1) * 10.0;
        $amount = round($base + $extra, 2);

        $labels = [
            'nova_poshta' => 'Нова Пошта',
            'ukrposhta' => 'Укрпошта',
            'meest' => 'Meest',
            'courier' => 'Курʼєр',
            'pickup' => 'Самовивіз',
        ];

        return [
            'amount' => $amount,
            'label' => $amount > 0
                ? number_format($amount, 0, '.', ' ') . ' грн'
                : 'Безкоштовно',
            'note' => ($labels[$carrier] ?? 'Доставка') . ' · орієнтовна вартість',
        ];
    }

    public function estimateForCart(array $cart, ?string $carrier = null): array
    {
        $subtotal = 0.0;
        $itemCount = 0;

        foreach ($cart as $item) {
            $subtotal += (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0);
            $itemCount += (int) ($item['quantity'] ?? 0);
        }

        $carrier = $carrier ?: 'nova_poshta';

        return array_merge(
            $this->calculate($carrier, $subtotal, $itemCount),
            ['subtotal' => $subtotal, 'carrier' => $carrier, 'item_count' => $itemCount]
        );
    }
}
