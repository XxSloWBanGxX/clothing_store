<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);

        $cartItems = [];
        $total = 0;

        foreach ($cart as $item) {
            $subtotal = (float) $item['price'] * (int) $item['quantity'];
            $total += $subtotal;

            $cartItems[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'image' => $item['image'] ?? null,
                'quantity' => $item['quantity'],
                'subtotal' => $subtotal,
            ];
        }

        return view('cart', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $productId = (int) $request->input('product_id');
        $product = DB::table('products')->where('id', $productId)->first();

        if (! $product) {
            return back()->with('success', 'Товар не знайдено');
        }

        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => 1,
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Додано в кошик!');
    }

    public function remove(Request $request)
    {
        $productId = (int) $request->input('product_id');
        $cart = session('cart', []);

        unset($cart[$productId]);

        session(['cart' => $cart]);

        return redirect('/cart');
    }
}
