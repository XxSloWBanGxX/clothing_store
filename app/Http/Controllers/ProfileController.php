<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $orders = DB::table('orders')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('profile', compact('user', 'orders'));
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

        // Видаляємо старий аватар
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

        return back()->with('avatarSuccess', 'Аватар оновлено');
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

        return back()->with('passwordSuccess', 'Пароль успішно змінено');
    }

    public function order($id)
    {
        $user = Auth::user();

        $order = DB::table('orders')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $order) {
            return redirect('/profile');
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
            return redirect('/profile');
        }

        if (! in_array($order->status, ['new', 'processing'], true)) {
            return back()->with('orderError', 'Це замовлення вже не можна скасувати');
        }

        DB::transaction(function () use ($id) {
            DB::table('orders')->where('id', $id)->update(['status' => 'cancelled']);

            // Повертаємо залишки на склад
            $items = DB::table('order_items')->where('order_id', $id)->get();
            foreach ($items as $item) {
                DB::table('products')
                    ->where('id', $item->product_id)
                    ->increment('stock', (int) $item->quantity);
            }
        });

        return back()->with('orderSuccess', 'Замовлення скасовано');
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

        return back()->with('profileSuccess', 'Дані оновлено');
    }
}
