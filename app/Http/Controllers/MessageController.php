<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MessageController extends Controller
{
    public function reply(Request $request, $id)
    {
        $userId = Auth::id();

        $conversation = DB::table('conversations')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (! $conversation) {
            abort(404);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:5000'],
        ], [
            'body.required' => 'Напиши повідомлення',
        ]);

        DB::table('conversation_messages')->insert([
            'conversation_id' => (int) $id,
            'sender_role' => 'user',
            'sender_user_id' => $userId,
            'body' => trim($validated['body']),
            'created_at' => now(),
        ]);

        DB::table('conversations')->where('id', $id)->update([
            'status' => 'open',
            'last_message_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/profile?tab=messages&conversation=' . $id)->with('messageSuccess', 'Повідомлення надіслано');
    }
}
