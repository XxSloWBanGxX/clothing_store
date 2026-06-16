<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function __construct(private PricingService $pricing)
    {
    }

    public function index()
    {
        $cart = session('cart', []);

        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = DB::table('products')->where('id', $productId)->first();
            if ($product) {
                $effectivePrice = $this->pricing->getEffectivePrice($product);
                $item['price'] = $effectivePrice;
                $cart[$productId]['price'] = $effectivePrice;
            }

            $subtotal = (float) $item['price'] * (int) $item['quantity'];
            $total += $subtotal;

            $cartItems[] = array_merge($item, ['subtotal' => $subtotal]);
        }

        session(['cart' => $cart]);

        return view('cart', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $productId = (int) $request->input('product_id');
        $product = DB::table('products')->where('id', $productId)->first();

        if (! $product) {
            return back()->with('success', 'Товар не знайдено');
        }

        if ((int) $product->stock <= 0) {
            return back()->with('stockError', 'Цього товару немає в наявності');
        }

        $qty = max(1, (int) $request->input('quantity', 1));
        $cart = session('cart', []);

        $currentInCart = isset($cart[$productId]) ? (int) $cart[$productId]['quantity'] : 0;

        if ($currentInCart + $qty > (int) $product->stock) {
            return back()->with('stockError', 'Недостатньо товару на складі. Доступно: ' . (int) $product->stock . ' шт.');
        }

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $qty;
            $cart[$productId]['price'] = $this->pricing->getEffectivePrice($product);
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $this->pricing->getEffectivePrice($product),
                'image' => $product->image,
                'quantity' => $qty,
                'selected_size' => $request->input('selected_size'),
                'selected_color_name' => $request->input('selected_color_name'),
                'selected_color_hex' => $request->input('selected_color_hex'),
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Додано в кошик!');
    }

    public function updateQty(Request $request)
    {
        $productId = (int) $request->input('product_id');
        $action = $request->input('action');
        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            if ($action === 'increase') {
                $cart[$productId]['quantity']++;
            } elseif ($action === 'decrease') {
                $cart[$productId]['quantity']--;

                if ($cart[$productId]['quantity'] < 1) {
                    unset($cart[$productId]);
                }
            }
        }

        session(['cart' => $cart]);

        return redirect('/cart');
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
