<?php

namespace App\Http\Controllers;

use App\Services\PresenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminMessageController extends Controller
{
    private function conversationUserColumns(): array
    {
        $columns = [
            'users.name as user_name',
            'users.username as user_username',
            'users.email as user_email',
        ];

        if (Schema::hasColumn('users', 'last_seen_at')) {
            $columns[] = 'users.last_seen_at as user_last_seen_at';
        }

        return $columns;
    }

    public function index()
    {
        if (! Schema::hasTable('conversations')) {
            return view('admin.messages', ['conversations' => collect(), 'statusFilter' => '', 'users' => collect()]);
        }

        $statusFilter = trim((string) request()->query('status', ''));

        $query = DB::table('conversations')
            ->leftJoin('users', 'users.id', '=', 'conversations.user_id')
            ->select(array_merge(['conversations.*'], $this->conversationUserColumns()))
            ->orderByDesc('conversations.last_message_at')
            ->orderByDesc('conversations.id');

        if ($statusFilter === 'open') {
            $query->where('conversations.status', 'open');
        } elseif ($statusFilter === 'closed') {
            $query->where('conversations.status', 'closed');
        }

        $conversations = $query->get()->map(function ($row) {
            $row->unread_count = (int) DB::table('conversation_messages')
                ->where('conversation_id', $row->id)
                ->where('sender_role', 'user')
                ->whereNull('read_at')
                ->count();

            $row->last_preview = DB::table('conversation_messages')
                ->where('conversation_id', $row->id)
                ->orderByDesc('id')
                ->value('body');

            $row->user_online = PresenceService::isOnline(null, $row->user_last_seen_at ?? null);

            return $row;
        });

        $users = DB::table('users')->where('role', 'user')->orderBy('name')->get(['id', 'name', 'email']);

        $onlineUsersCount = PresenceService::onlineUsersCount();

        return view('admin.messages', compact('conversations', 'statusFilter', 'users', 'onlineUsersCount'));
    }

    public function show($id)
    {
        $conversation = DB::table('conversations')
            ->leftJoin('users', 'users.id', '=', 'conversations.user_id')
            ->select(array_merge(['conversations.*'], array_merge($this->conversationUserColumns(), [
                'users.phone as user_phone',
            ])))
            ->where('conversations.id', $id)
            ->first();

        if (! $conversation) {
            abort(404);
        }

        $userOnline = PresenceService::isOnline(null, $conversation->user_last_seen_at ?? null);

        DB::table('conversation_messages')
            ->where('conversation_id', $id)
            ->where('sender_role', 'user')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = DB::table('conversation_messages')
            ->where('conversation_id', $id)
            ->orderBy('id')
            ->get();

        return view('admin.message-show', compact('conversation', 'messages', 'userOnline'));
    }

    public function reply(Request $request, $id)
    {
        $conversation = DB::table('conversations')->where('id', $id)->first();

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
            'sender_role' => 'admin',
            'sender_user_id' => Auth::id(),
            'body' => trim($validated['body']),
            'created_at' => now(),
        ]);

        DB::table('conversations')->where('id', $id)->update([
            'status' => 'open',
            'last_message_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/messages/' . $id)->with('status', 'Відповідь надіслано');
    }

    public function close($id)
    {
        DB::table('conversations')->where('id', $id)->update([
            'status' => 'closed',
            'updated_at' => now(),
        ]);

        return redirect('/admin/messages/' . $id)->with('status', 'Діалог закрито');
    }

    public function start(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'body' => ['required', 'string', 'min:1', 'max:5000'],
        ]);

        $user = DB::table('users')->where('id', $validated['user_id'])->first();

        $conversationId = DB::table('conversations')->insertGetId([
            'user_id' => (int) $validated['user_id'],
            'guest_name' => $user->name,
            'guest_email' => $user->email,
            'subject' => 'Повідомлення від адміністратора',
            'status' => 'open',
            'last_message_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('conversation_messages')->insert([
            'conversation_id' => $conversationId,
            'sender_role' => 'admin',
            'sender_user_id' => Auth::id(),
            'body' => trim($validated['body']),
            'created_at' => now(),
        ]);

        return redirect('/admin/messages/' . $conversationId)->with('status', 'Діалог розпочато');
    }
}
