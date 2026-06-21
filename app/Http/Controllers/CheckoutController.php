<?php

namespace App\Http\Controllers;

use App\Services\CardPaymentService;
use App\Services\Delivery\UserDeliveryStorage;
use App\Services\PricingService;
use App\Services\PromoService;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct(
        private PricingService $pricing,
        private ShippingService $shipping,
    ) {
    }

    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect('/cart');
        }

        [$cartItems, $total] = $this->buildCartTotals($cart);
        $itemCount = array_sum(array_column($cartItems, 'quantity'));
        $carrier = old('delivery_carrier', 'nova_poshta');
        $shipping = $this->shipping->estimateForCart(session('cart', []), $carrier);

        $user = Auth::user();
        $userRow = $user ? DB::table('users')->where('id', $user->id)->first() : null;
        $deliverySaved = $userRow
            ? UserDeliveryStorage::pickerSaved($userRow, old('delivery_carrier'))
            : [];

        $discount = 0.0;
        $promoApplied = null;
        $promoCode = trim((string) (old('promo_code') ?? ''));

        if ($promoCode !== '') {
            $promoService = app(PromoService::class);
            $promoApplied = $promoService->validate($promoCode, (int) (Auth::id() ?? 0), $total);
            if ($promoApplied['valid'] ?? false) {
                $discount = (float) $promoApplied['discount_amount'];
            }
        }

        $finalTotal = max(0, $total - $discount + (float) $shipping['amount']);

        return view('checkout', compact(
            'cartItems',
            'total',
            'finalTotal',
            'discount',
            'promoApplied',
            'promoCode',
            'user',
            'userRow',
            'deliverySaved',
            'shipping',
            'itemCount',
        ));
    }

    public function store(Request $request, CardPaymentService $cardPayment, PromoService $promoService)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect('/cart');
        }

        $rules = [
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'address_line' => ['required', 'string', 'max:255'],
            'delivery_carrier' => ['required', Rule::in(['nova_poshta', 'ukrposhta', 'meest', 'courier', 'pickup'])],
            'delivery_method' => ['required', Rule::in(['nova_poshta', 'ukrposhta', 'meest', 'courier', 'pickup'])],
            'payment_method' => ['required', Rule::in(['cash_on_delivery', 'card'])],
            'comment' => ['nullable', 'string'],
            'delivery_city_ref' => ['nullable', 'string', 'max:64'],
            'delivery_branch_ref' => ['nullable', 'string', 'max:64'],
            'promo_code' => ['nullable', 'string', 'max:32'],
        ];

        if ($request->input('payment_method') === 'card') {
            $rules['card_number'] = ['required', 'string', 'max:24'];
            $rules['card_expiry'] = ['required', 'string', 'max:5'];
            $rules['card_cvv'] = ['required', 'string', 'max:4'];
            $rules['card_holder'] = ['required', 'string', 'max:120'];
        }

        $validated = $request->validate($rules, [
            'full_name.required' => 'Введи імʼя та прізвище',
            'phone.required' => 'Введи номер телефону',
            'email.required' => 'Введи email',
            'email.email' => 'Некоректний email',
            'city.required' => 'Введи місто',
            'address_line.required' => 'Введи адресу або відділення',
            'delivery_method.required' => 'Оберіть спосіб доставки',
            'payment_method.required' => 'Оберіть спосіб оплати',
        ]);

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

        [$cartItems, $total] = $this->buildCartTotals($cart);
        $itemCount = array_sum(array_column($cartItems, 'quantity'));
        $shippingQuote = $this->shipping->calculate(
            $validated['delivery_carrier'],
            $total,
            $itemCount
        );
        $shippingAmount = (float) $shippingQuote['amount'];

        $discount = 0.0;
        $promoCode = null;
        $promoData = null;

        if ($request->filled('promo_code')) {
            $promoData = $promoService->validate($request->input('promo_code'), (int) (Auth::id() ?? 0), $total);

            if (! ($promoData['valid'] ?? false)) {
                return back()->withInput()->withErrors(['promo_code' => $promoData['message'] ?? 'Некоректний промокод']);
            }

            $discount = (float) $promoData['discount_amount'];
            $promoCode = $promoData['code'];
        }

        $payableTotal = max(0, $total - $discount + $shippingAmount);

        $paymentStatus = 'pending';
        $orderStatus = 'new';
        $cardLast4 = null;
        $paymentReference = null;

        if ($validated['payment_method'] === 'card') {
            try {
                $card = $cardPayment->validate($validated);
                $payment = $cardPayment->charge($payableTotal, $card);
                $paymentStatus = 'paid';
                $orderStatus = 'processing';
                $cardLast4 = $payment['last4'];
                $paymentReference = $payment['reference'];
            } catch (ValidationException $e) {
                return back()->withInput()->withErrors($e->errors());
            }
        }

        $userId = Auth::id();

        $orderId = DB::transaction(function () use ($cart, $validated, $payableTotal, $discount, $promoCode, $paymentStatus, $orderStatus, $cardLast4, $paymentReference, $userId, $shippingAmount) {
            $orderData = [
                'user_id' => $userId,
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'city' => $validated['city'],
                'address_line' => $validated['address_line'],
                'delivery_method' => $validated['delivery_method'] ?? $validated['delivery_carrier'],
                'payment_method' => $validated['payment_method'],
                'comment' => $validated['comment'] ?? '',
                'total_amount' => $payableTotal,
                'status' => $orderStatus,
                'created_at' => now(),
            ];

            if (Schema::hasColumn('orders', 'shipping_amount')) {
                $orderData['shipping_amount'] = $shippingAmount;
            }
            if (Schema::hasColumn('orders', 'discount_amount')) {
                $orderData['discount_amount'] = $discount;
            }
            if (Schema::hasColumn('orders', 'promocode')) {
                $orderData['promocode'] = $promoCode;
            }
            if (Schema::hasColumn('orders', 'payment_status')) {
                $orderData['payment_status'] = $paymentStatus;
                $orderData['card_last4'] = $cardLast4;
                $orderData['payment_reference'] = $paymentReference;
            }

            $orderId = DB::table('orders')->insertGetId($orderData);

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

                DB::table('products')
                    ->where('id', $item['id'])
                    ->where('stock', '>=', (int) $item['quantity'])
                    ->decrement('stock', (int) $item['quantity']);
            }

            return $orderId;
        });

        if ($promoData && ($promoData['valid'] ?? false)) {
            $promoService->markUsed($promoData);
        }

        if ($userId && Schema::hasColumn('users', 'delivery_city')) {
            UserDeliveryStorage::save($userId, $validated['delivery_carrier'], [
                'city' => $validated['city'],
                'branch' => $validated['address_line'],
                'city_ref' => $validated['delivery_city_ref'] ?? '',
                'branch_ref' => $validated['delivery_branch_ref'] ?? '',
                'manual' => (bool) $request->boolean('manual_address'),
            ]);
        }

        session()->forget('cart');
        session(['last_guest_order_id' => $orderId]);

        return redirect('/checkout/success/' . $orderId);
    }

    public function success($id)
    {
        $order = null;

        if (Auth::check()) {
            $order = DB::table('orders')
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->first();
        }

        if (! $order && (int) session('last_guest_order_id') === (int) $id) {
            $order = DB::table('orders')->where('id', $id)->first();
        }

        if (! $order) {
            return redirect('/');
        }

        return view('checkout-success', compact('order'));
    }

    private function buildCartTotals(array $cart): array
    {
        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = DB::table('products')->where('id', $productId)->first();
            if ($product) {
                $item['price'] = $this->pricing->getEffectivePrice($product);
                $cart[$productId]['price'] = $item['price'];
            }

            $subtotal = (float) $item['price'] * (int) $item['quantity'];
            $total += $subtotal;
            $cartItems[] = array_merge($item, ['subtotal' => $subtotal]);
        }

        session(['cart' => $cart]);

        return [$cartItems, $total];
    }
}
