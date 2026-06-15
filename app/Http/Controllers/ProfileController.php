<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
}
