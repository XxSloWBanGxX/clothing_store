<?php

namespace App\Services\Delivery;

class DeliveryManager
{
    public function __construct(
        private NovaPoshtaService $novaPoshta,
        private UkrposhtaDeliveryService $ukrposhta,
        private MeestDeliveryService $meest,
    ) {}

    public function carriers(): array
    {
        return [
            'nova_poshta' => [
                'label' => 'Нова Пошта',
                'configured' => true,
            ],
            'ukrposhta' => [
                'label' => 'Укрпошта',
                'configured' => $this->ukrposhta->isConfigured(),
            ],
            'meest' => [
                'label' => 'Meest',
                'configured' => $this->meest->isConfigured(),
            ],
        ];
    }

    public function searchCities(string $carrier, string $query): array
    {
        $query = trim($query);
        if (mb_strlen($query) < 2) {
            return [];
        }

        return match ($carrier) {
            'nova_poshta' => $this->novaPoshta->searchCities($query),
            'ukrposhta' => $this->ukrposhta->searchCities($query),
            'meest' => $this->meest->searchCities($query),
            default => [],
        };
    }

    public function searchPoints(string $carrier, string $cityRef, string $query = ''): array
    {
        if ($cityRef === '') {
            return [];
        }

        return match ($carrier) {
            'nova_poshta' => $this->novaPoshta->searchPoints($cityRef, $query),
            'ukrposhta' => $this->ukrposhta->searchPoints($cityRef, $query),
            'meest' => $this->meest->searchPoints($cityRef, $query),
            default => [],
        };
    }
}
