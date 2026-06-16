<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminPromocodeController extends Controller
{
    public function index()
    {
        $promocodes = Schema::hasTable('promocodes')
            ? DB::table('promocodes')->orderByDesc('id')->get()
            : collect();

        return view('admin.promocodes', compact('promocodes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:32', Rule::unique('promocodes', 'code')],
            'title' => ['required', 'string', 'max:120'],
            'discount_percent' => ['required', 'integer', 'min:1', 'max:90'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ], [
            'code.required' => 'Введи код промокоду',
            'code.unique' => 'Такий промокод вже існує',
            'title.required' => 'Введи назву акції',
            'discount_percent.required' => 'Вкажи відсоток знижки',
        ]);

        DB::table('promocodes')->insert([
            'code' => strtoupper(trim($validated['code'])),
            'title' => $validated['title'],
            'discount_percent' => (int) $validated['discount_percent'],
            'min_order_amount' => $validated['min_order_amount'] ?: null,
            'max_uses' => $validated['max_uses'] ?: null,
            'uses_count' => 0,
            'starts_at' => $validated['starts_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => $request->boolean('is_active', true) ? 1 : 0,
            'created_at' => now(),
        ]);

        return redirect('/admin/promocodes')->with('status', 'Промокод створено');
    }

    public function update(Request $request, $id)
    {
        $promo = DB::table('promocodes')->where('id', $id)->first();

        if (! $promo) {
            abort(404);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:32', Rule::unique('promocodes', 'code')->ignore($id)],
            'title' => ['required', 'string', 'max:120'],
            'discount_percent' => ['required', 'integer', 'min:1', 'max:90'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
        ]);

        DB::table('promocodes')->where('id', $id)->update([
            'code' => strtoupper(trim($validated['code'])),
            'title' => $validated['title'],
            'discount_percent' => (int) $validated['discount_percent'],
            'min_order_amount' => $validated['min_order_amount'] ?: null,
            'max_uses' => $validated['max_uses'] ?: null,
            'starts_at' => $validated['starts_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => $request->boolean('is_active') ? 1 : 0,
        ]);

        return redirect('/admin/promocodes')->with('status', 'Промокод оновлено');
    }

    public function destroy($id)
    {
        DB::table('promocodes')->where('id', $id)->delete();

        return redirect('/admin/promocodes')->with('status', 'Промокод видалено');
    }
}
