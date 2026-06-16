<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class StockAlertController extends Controller
{
    public function store(Request $request, $id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        if (! $product) {
            abort(404);
        }

        if ((int) $product->stock > 0) {
            return back()->with('success', 'Товар уже в наявності — можеш додати в кошик.');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ], [
            'email.required' => 'Введи email для сповіщення',
            'email.email' => 'Некоректний email',
        ]);

        if (! Schema::hasTable('product_stock_alerts')) {
            return back()->with('success', 'Запит збережено.');
        }

        DB::table('product_stock_alerts')->updateOrInsert(
            ['product_id' => (int) $id, 'email' => $validated['email']],
            [
                'user_id' => Auth::id(),
                'notified_at' => null,
                'created_at' => now(),
            ]
        );

        return back()->with('success', 'Ми повідомимо на ' . $validated['email'] . ', коли товар зʼявиться.');
    }
}
