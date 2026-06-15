<?php

namespace App\Http\Controllers;

use App\Services\CardPaymentService;
use App\Services\Delivery\UserDeliveryStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
        $userRow = DB::table('users')->where('id', $user->id)->first();
        $deliverySaved = UserDeliveryStorage::pickerSaved($userRow, old('delivery_carrier'));

        return view('checkout', compact('cartItems', 'total', 'user', 'userRow', 'deliverySaved'));
    }

    public function store(Request $request, CardPaymentService $cardPayment)
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
            'card_number.required' => 'Введи номер картки',
            'card_expiry.required' => 'Введи термін дії',
            'card_cvv.required' => 'Введи CVV',
            'card_holder.required' => 'Введи імʼя на картці',
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

        $total = 0;
        foreach ($cart as $item) {
            $total += (float) $item['price'] * (int) $item['quantity'];
        }

        $paymentStatus = 'pending';
        $orderStatus = 'new';
        $cardLast4 = null;
        $paymentReference = null;

        if ($validated['payment_method'] === 'card') {
            try {
                $card = $cardPayment->validate($validated);
                $payment = $cardPayment->charge($total, $card);
                $paymentStatus = 'paid';
                $orderStatus = 'processing';
                $cardLast4 = $payment['last4'];
                $paymentReference = $payment['reference'];
            } catch (ValidationException $e) {
                return back()->withInput()->withErrors($e->errors());
            }
        }

        $orderId = DB::transaction(function () use ($cart, $validated, $total, $paymentStatus, $orderStatus, $cardLast4, $paymentReference) {
            $orderData = [
                'user_id' => Auth::id(),
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'city' => $validated['city'],
                'address_line' => $validated['address_line'],
                'delivery_method' => $validated['delivery_method'] ?? $validated['delivery_carrier'],
                'payment_method' => $validated['payment_method'],
                'comment' => $validated['comment'] ?? '',
                'total_amount' => $total,
                'status' => $orderStatus,
                'created_at' => now(),
            ];

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

        if (Schema::hasColumn('users', 'delivery_city')) {
            UserDeliveryStorage::save(Auth::id(), $validated['delivery_carrier'], [
                'city' => $validated['city'],
                'branch' => $validated['address_line'],
                'city_ref' => $validated['delivery_city_ref'] ?? '',
                'branch_ref' => $validated['delivery_branch_ref'] ?? '',
                'manual' => (bool) $request->boolean('manual_address'),
            ]);
        }

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
