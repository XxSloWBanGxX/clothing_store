<?php

namespace App\Http\Controllers;

use App\Services\ShippingService;
use Illuminate\Http\Request;

class ShippingQuoteController extends Controller
{
    public function __construct(private ShippingService $shipping)
    {
    }

    public function quote(Request $request)
    {
        $carrier = (string) $request->query('carrier', 'nova_poshta');
        $cart = session('cart', []);

        if (empty($cart)) {
            return response()->json(['amount' => 0, 'label' => '—', 'note' => 'Кошик порожній']);
        }

        $quote = $this->shipping->estimateForCart($cart, $carrier);

        return response()->json([
            'amount' => $quote['amount'],
            'label' => $quote['label'],
            'note' => $quote['note'],
            'subtotal' => $quote['subtotal'],
            'total' => round($quote['subtotal'] + $quote['amount'], 2),
        ]);
    }
}
