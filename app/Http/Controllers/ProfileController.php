<?php

namespace App\Http\Controllers;

use App\Services\Delivery\UserDeliveryStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userRow = DB::table('users')->where('id', $user->id)->first();

        $this->ensureWelcomePromo($user->id);

        $orders = DB::table('orders')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        $reviews = DB::table('reviews')
            ->leftJoin('products', 'products.id', '=', 'reviews.product_id')
            ->where('reviews.user_id', $user->id)
            ->orderBy('reviews.id', 'desc')
            ->select('reviews.*', 'products.name as product_name', 'products.slug as product_slug')
            ->get();

        $promocodes = Schema::hasTable('user_promocodes')
            ? DB::table('user_promocodes')->where('user_id', $user->id)->orderBy('id', 'desc')->get()
            : collect();

        $bonusHistory = Schema::hasTable('bonus_history')
            ? DB::table('bonus_history')->where('user_id', $user->id)->orderBy('id', 'desc')->get()
            : collect();

        $bonusPoints = $userRow->bonus_points ?? 0;
        $activeTab = request('tab', 'settings');
        $deliverySaved = UserDeliveryStorage::pickerSaved($userRow, old('delivery_carrier'));

        if (! in_array($activeTab, ['settings', 'orders', 'promos', 'bonus', 'reviews'], true)) {
            $activeTab = 'settings';
        }

        return view('profile', compact(
            'user',
            'userRow',
            'orders',
            'reviews',
            'promocodes',
            'bonusHistory',
            'bonusPoints',
            'activeTab',
            'deliverySaved'
        ));
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'avatar.required' => 'Обери зображення',
            'avatar.image' => 'Файл має бути зображенням',
            'avatar.mimes' => 'Дозволені jpg, jpeg, png, webp',
            'avatar.max' => 'Максимальний розмір — 4 МБ',
        ]);

        $user = Auth::user();

        $dir = public_path('assets/images/avatars');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (! empty($user->avatar)) {
            $oldPath = $dir . DIRECTORY_SEPARATOR . $user->avatar;
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        $file = $request->file('avatar');
        $name = 'avatar_' . $user->id . '_' . time() . '.' . strtolower($file->getClientOriginalExtension());
        $file->move($dir, $name);

        DB::table('users')->where('id', $user->id)->update(['avatar' => $name]);

        return redirect('/profile?tab=settings')->with('avatarSuccess', 'Аватар оновлено');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6'],
            'confirm_password' => ['required', 'same:new_password'],
        ], [
            'current_password.required' => 'Введи поточний пароль',
            'new_password.required' => 'Введи новий пароль',
            'new_password.min' => 'Новий пароль має містити мінімум 6 символів',
            'confirm_password.required' => 'Підтверди новий пароль',
            'confirm_password.same' => 'Паролі не співпадають',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Поточний пароль неправильний']);
        }

        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make($request->input('new_password')),
        ]);

        return redirect('/profile?tab=settings')->with('passwordSuccess', 'Пароль успішно змінено');
    }

    public function order($id)
    {
        $user = Auth::user();

        $order = DB::table('orders')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $order) {
            return redirect('/profile?tab=orders');
        }

        $items = DB::table('order_items')->where('order_id', $id)->orderBy('id')->get();

        return view('profile-order', compact('order', 'items'));
    }

    public function cancelOrder($id)
    {
        $user = Auth::user();

        $order = DB::table('orders')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $order) {
            return redirect('/profile?tab=orders');
        }

        if (! in_array($order->status, ['new', 'processing'], true)) {
            return redirect('/profile?tab=orders')->with('orderError', 'Це замовлення вже не можна скасувати');
        }

        DB::transaction(function () use ($id) {
            DB::table('orders')->where('id', $id)->update(['status' => 'cancelled']);

            $items = DB::table('order_items')->where('order_id', $id)->get();
            foreach ($items as $item) {
                DB::table('products')
                    ->where('id', $item->product_id)
                    ->increment('stock', (int) $item->quantity);
            }
        });

        return redirect('/profile?tab=orders')->with('orderSuccess', 'Замовлення #' . $id . ' скасовано');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'min:3', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
        ], [
            'name.required' => 'Введи імʼя',
            'username.required' => 'Введи username',
            'username.unique' => 'Цей username вже зайнятий',
            'email.required' => 'Введи email',
            'email.email' => 'Некоректний email',
            'email.unique' => 'Цей email вже зайнятий',
            'phone.required' => 'Введи телефон',
            'phone.unique' => 'Цей телефон вже зайнятий',
        ]);

        DB::table('users')->where('id', $user->id)->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        return redirect('/profile?tab=settings')->with('profileSuccess', 'Дані оновлено');
    }

    public function updateDelivery(Request $request)
    {
        $validated = $request->validate([
            'delivery_carrier' => ['required', 'in:nova_poshta,ukrposhta,meest,courier,pickup'],
            'delivery_city' => ['required', 'string', 'max:255'],
            'delivery_branch' => ['required', 'string', 'max:255'],
            'delivery_city_ref' => ['nullable', 'string', 'max:64'],
            'delivery_branch_ref' => ['nullable', 'string', 'max:64'],
            'manual_address' => ['nullable', 'boolean'],
        ], [
            'delivery_carrier.required' => 'Обери перевізника',
            'delivery_city.required' => 'Обери або введи місто',
            'delivery_branch.required' => 'Обери або введи відділення',
        ]);

        UserDeliveryStorage::save(Auth::id(), $validated['delivery_carrier'], [
            'city' => $validated['delivery_city'],
            'branch' => $validated['delivery_branch'],
            'city_ref' => $validated['delivery_city_ref'] ?? '',
            'branch_ref' => $validated['delivery_branch_ref'] ?? '',
            'manual' => (bool) ($validated['manual_address'] ?? false),
        ]);

        return redirect('/profile?tab=settings')->with('deliverySuccess', 'Адресу для обраного перевізника збережено');
    }

    private function ensureWelcomePromo(int $userId): void
    {
        if (! Schema::hasTable('user_promocodes')) {
            return;
        }

        $exists = DB::table('user_promocodes')->where('user_id', $userId)->exists();

        if ($exists) {
            return;
        }

        DB::table('user_promocodes')->insert([
            'user_id' => $userId,
            'code' => 'WELCOME10',
            'title' => 'Вітальна знижка 10%',
            'discount_percent' => 10,
            'expires_at' => now()->addMonths(3),
            'created_at' => now(),
        ]);

        if (Schema::hasTable('bonus_history')) {
            DB::table('bonus_history')->insert([
                'user_id' => $userId,
                'points' => 100,
                'type' => 'accrual',
                'description' => 'Бонус за реєстрацію',
                'created_at' => now(),
            ]);
        }

        if (Schema::hasColumn('users', 'bonus_points')) {
            DB::table('users')->where('id', $userId)->increment('bonus_points', 100);
        }
    }
}
