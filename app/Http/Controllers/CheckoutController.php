<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect('/cart');
        }

        $cartItems = [];
        $total = 0;

        foreach ($cart as $item) {
            $subtotal = (float) $item['price'] * (int) $item['quantity'];
            $total += $subtotal;
            $cartItems[] = array_merge($item, ['subtotal' => $subtotal]);
        }

        $user = Auth::user();

        return view('checkout', compact('cartItems', 'total', 'user'));
    }

    public function store(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect('/cart');
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'address_line' => ['required', 'string', 'max:255'],
            'delivery_method' => ['required', Rule::in(['nova_poshta', 'courier', 'pickup'])],
            'payment_method' => ['required', Rule::in(['cash_on_delivery', 'card'])],
            'comment' => ['nullable', 'string'],
        ], [
            'full_name.required' => 'Введи імʼя та прізвище',
            'phone.required' => 'Введи номер телефону',
            'email.required' => 'Введи email',
            'email.email' => 'Некоректний email',
            'city.required' => 'Введи місто',
            'address_line.required' => 'Введи адресу або відділення',
            'delivery_method.required' => 'Оберіть спосіб доставки',
            'payment_method.required' => 'Оберіть спосіб оплати',
        ]);

        // Перевірка наявності перед оформленням
        $stockErrors = [];
        foreach ($cart as $item) {
            $current = DB::table('products')->where('id', $item['id'])->value('stock');

            if ($current === null) {
                $stockErrors[] = "Товар «{$item['name']}» більше недоступний";
            } elseif ((int) $current < (int) $item['quantity']) {
                $stockErrors[] = "Товару «{$item['name']}» залишилось лише {$current} шт.";
            }
        }

        if (! empty($stockErrors)) {
            return redirect('/cart')->with('stockError', implode(' ', $stockErrors));
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += (float) $item['price'] * (int) $item['quantity'];
        }

        $orderId = DB::transaction(function () use ($cart, $validated, $total) {
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => Auth::id(),
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'city' => $validated['city'],
                'address_line' => $validated['address_line'],
                'delivery_method' => $validated['delivery_method'],
                'payment_method' => $validated['payment_method'],
                'comment' => $validated['comment'] ?? '',
                'total_amount' => $total,
                'status' => 'new',
                'created_at' => now(),
            ]);

            foreach ($cart as $item) {
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'product_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'selected_size' => $item['selected_size'] ?? null,
                    'selected_color_name' => $item['selected_color_name'] ?? null,
                    'selected_color_hex' => $item['selected_color_hex'] ?? null,
                    'created_at' => now(),
                ]);

                // Списання залишків
                DB::table('products')
                    ->where('id', $item['id'])
                    ->where('stock', '>=', (int) $item['quantity'])
                    ->decrement('stock', (int) $item['quantity']);
            }

            return $orderId;
        });

        session()->forget('cart');

        return redirect('/checkout/success/' . $orderId);
    }

    public function success($id)
    {
        $order = DB::table('orders')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (! $order) {
            return redirect('/');
        }

        return view('checkout-success', compact('order'));
    }
}
