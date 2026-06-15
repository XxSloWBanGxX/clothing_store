<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = DB::table('orders')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.*', 'users.username as username')
            ->orderBy('orders.id', 'desc')
            ->get();

        return view('admin.orders', compact('orders'));
    }

    public function show($id)
    {
        $order = DB::table('orders')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.*', 'users.username as username')
            ->where('orders.id', $id)
            ->first();

        if (! $order) {
            abort(404, 'Замовлення не знайдено');
        }

        $items = DB::table('order_items')->where('order_id', $id)->get();

        return view('admin.order-show', compact('order', 'items'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['new', 'processing', 'sent', 'completed', 'cancelled'])],
        ]);

        DB::table('orders')->where('id', $id)->update([
            'status' => $validated['status'],
        ]);

        return redirect('/admin/orders/' . $id)->with('status', 'Статус оновлено');
    }
}
