<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('orders')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.*', 'users.username as username');

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('orders.full_name', 'like', '%' . $search . '%')
                    ->orWhere('orders.phone', 'like', '%' . $search . '%')
                    ->orWhere('orders.email', 'like', '%' . $search . '%');

                if (ctype_digit($search)) {
                    $builder->orWhere('orders.id', (int) $search);
                }
            });
        }

        $statusFilter = trim((string) $request->query('status', ''));
        if ($statusFilter !== '') {
            $query->where('orders.status', $statusFilter);
        }

        $orders = $query->orderByDesc('orders.id')->get();

        $statusLabels = AdminController::orderStatusLabels();
        $filters = [
            'q' => $search,
            'status' => $statusFilter,
        ];

        return view('admin.orders', compact('orders', 'statusLabels', 'filters'));
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
        $statusLabels = AdminController::orderStatusLabels();
        $deliveryLabels = AdminController::deliveryLabels();
        $paymentLabels = AdminController::paymentLabels();

        return view('admin.order-show', compact('order', 'items', 'statusLabels', 'deliveryLabels', 'paymentLabels'));
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
