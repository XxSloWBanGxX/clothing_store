<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Введи email, телефон або username',
            'password.required' => 'Введи пароль',
        ]);

        $login = trim($request->input('login'));
        $password = $request->input('password');

        $user = User::where('email', $login)
            ->orWhere('username', $login)
            ->orWhere('phone', $login)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return back()
                ->withInput($request->only('login'))
                ->withErrors(['general' => 'Невірний email, телефон, username або пароль']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->role === 'admin') {
            return redirect('/admin');
        }

        return redirect('/');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('users', 'username'),
            ],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')],
            'phone' => [
                'required',
                'string',
                'regex:/^\+?[0-9]{10,15}$/',
                Rule::unique('users', 'phone'),
            ],
            'password' => ['required', 'string', 'min:6'],
            'confirm_password' => ['required', 'same:password'],
        ], [
            'name.required' => 'Введи імʼя',
            'username.required' => 'Введи username',
            'username.regex' => 'Username: 3-20 символів, тільки букви, цифри, _',
            'username.unique' => 'Такий username вже зайнятий',
            'email.required' => 'Введи email',
            'email.email' => 'Некоректний email',
            'email.unique' => 'Користувач з таким email вже існує',
            'phone.required' => 'Введи номер телефону',
            'phone.regex' => 'Некоректний номер телефону',
            'phone.unique' => 'Користувач з таким номером вже існує',
            'password.required' => 'Введи пароль',
            'password.min' => 'Пароль має містити мінімум 6 символів',
            'confirm_password.required' => 'Підтверди пароль',
            'confirm_password.same' => 'Паролі не співпадають',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
            'role' => 'user',
            'is_verified' => false,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
