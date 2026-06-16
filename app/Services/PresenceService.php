<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PresenceService
{
    public const ONLINE_MINUTES = 5;

    public static function touch(?int $userId = null): void
    {
        $userId = $userId ?? Auth::id();
        if (! $userId || ! Schema::hasColumn('users', 'last_seen_at')) {
            return;
        }

        DB::table('users')->where('id', $userId)->update(['last_seen_at' => now()]);
    }

    public static function isOnline(?object $user, ?string $lastSeenAt = null): bool
    {
        $at = $lastSeenAt ?? ($user->last_seen_at ?? null);
        if (! $at) {
            return false;
        }

        return now()->diffInMinutes($at) <= self::ONLINE_MINUTES;
    }

    public static function onlineUsersCount(): int
    {
        if (! Schema::hasColumn('users', 'last_seen_at')) {
            return 0;
        }

        return (int) DB::table('users')
            ->where('last_seen_at', '>=', now()->subMinutes(self::ONLINE_MINUTES))
            ->where('role', 'user')
            ->count();
    }
}
