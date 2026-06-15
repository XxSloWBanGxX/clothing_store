<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CardPaymentService
{
    public function validate(array $input): array
    {
        $number = preg_replace('/\D+/', '', $input['card_number'] ?? '');
        $expiry = trim($input['card_expiry'] ?? '');
        $cvv = preg_replace('/\D+/', '', $input['card_cvv'] ?? '');
        $holder = trim($input['card_holder'] ?? '');

        $errors = [];

        if (strlen($number) < 13 || strlen($number) > 19) {
            $errors['card_number'] = 'Некоректний номер картки';
        } elseif (! $this->passesLuhn($number)) {
            $errors['card_number'] = 'Номер картки недійсний';
        }

        if (! preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $expiry, $matches)) {
            $errors['card_expiry'] = 'Термін у форматі MM/YY';
        } elseif (! $this->isExpiryValid((int) $matches[1], (int) $matches[2])) {
            $errors['card_expiry'] = 'Термін дії картки минув';
        }

        if (strlen($cvv) < 3 || strlen($cvv) > 4) {
            $errors['card_cvv'] = 'CVV має містити 3–4 цифри';
        }

        if ($holder === '' || mb_strlen($holder) < 3) {
            $errors['card_holder'] = 'Введи імʼя власника картки';
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return [
            'number' => $number,
            'expiry' => $expiry,
            'cvv' => $cvv,
            'holder' => $holder,
            'last4' => substr($number, -4),
        ];
    }

    public function charge(float $amount, array $card): array
    {
        // Демо-відмова для тесту: картка 4000...0002
        if (str_starts_with($card['number'], '4000000000000002')) {
            throw ValidationException::withMessages([
                'card_number' => 'Оплату відхилено банком. Спробуй іншу картку.',
            ]);
        }

        return [
            'success' => true,
            'last4' => $card['last4'],
            'reference' => 'PAY-' . strtoupper(Str::random(10)),
            'amount' => $amount,
        ];
    }

    private function passesLuhn(string $number): bool
    {
        $sum = 0;
        $alt = false;

        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $digit = (int) $number[$i];

            if ($alt) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
            $alt = ! $alt;
        }

        return $sum % 10 === 0;
    }

    private function isExpiryValid(int $month, int $year): bool
    {
        $year += 2000;
        $expiresAt = strtotime(sprintf('%04d-%02d-01', $year, $month) . ' +1 month -1 day');

        return $expiresAt >= strtotime('today');
    }
}
