<?php

namespace App\Http\Controllers;

use App\Services\Delivery\DeliveryManager;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function cities(Request $request, DeliveryManager $delivery)
    {
        $validated = $request->validate([
            'carrier' => ['required', 'in:nova_poshta,ukrposhta,meest'],
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        return response()->json([
            'items' => $delivery->searchCities($validated['carrier'], $validated['q']),
            'carriers' => $delivery->carriers(),
        ]);
    }

    public function points(Request $request, DeliveryManager $delivery)
    {
        $validated = $request->validate([
            'carrier' => ['required', 'in:nova_poshta,ukrposhta,meest'],
            'city_ref' => ['required', 'string', 'max:64'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        return response()->json([
            'items' => $delivery->searchPoints(
                $validated['carrier'],
                $validated['city_ref'],
                $validated['q'] ?? ''
            ),
        ]);
    }
}
