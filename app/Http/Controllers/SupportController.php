<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        if (Schema::hasTable('conversations')) {
            $conversationId = DB::table('conversations')->insertGetId([
                'user_id' => Auth::id(),
                'guest_name' => $validated['name'],
                'guest_email' => $validated['email'],
                'subject' => 'Звернення з сайту',
                'status' => 'open',
                'last_message_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('conversation_messages')->insert([
                'conversation_id' => $conversationId,
                'sender_role' => 'user',
                'sender_user_id' => Auth::id(),
                'body' => trim($validated['message']),
                'created_at' => now(),
            ]);
        }

        DB::table('support_messages')->insert([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'status' => 'new',
            'created_at' => now(),
        ]);

        if (Auth::check()) {
            return back()->with('supportSuccess', 'Дякуємо! Ми отримали повідомлення. Відповідь зʼявиться в профілі → «Повідомлення».');
        }

        return back()->with('supportSuccess', 'Дякуємо! Ми отримали ваше звернення і скоро відповімо.');
    }
}
