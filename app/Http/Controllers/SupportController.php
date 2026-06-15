<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'message' => ['required', 'string', 'min:5', 'max:3000'],
        ], [
            'name.required' => 'Введи імʼя',
            'email.required' => 'Введи email',
            'email.email' => 'Некоректний email',
            'message.required' => 'Напиши повідомлення',
            'message.min' => 'Повідомлення занадто коротке',
        ]);

        DB::table('support_messages')->insert([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'status' => 'new',
            'created_at' => now(),
        ]);

        return back()->with('supportSuccess', 'Дякуємо! Ми отримали ваше звернення і скоро відповімо.');
    }
}
