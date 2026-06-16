<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        if (! Schema::hasTable('newsletter_subscribers')) {
            return back()->with('newsletterError', 'Розсилка тимчасово недоступна');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ], [
            'email.required' => 'Введи email',
            'email.email' => 'Некоректний email',
        ]);

        $existing = DB::table('newsletter_subscribers')
            ->where('email', $validated['email'])
            ->first();

        if ($existing) {
            if (! $this->isActive($existing)) {
                $this->reactivate($existing->id);

                return back()->with('newsletterSuccess', 'Знову підписано на розсилку!');
            }

            return back()->with('newsletterSuccess', 'Ви вже підписані на розсилку');
        }

        $row = [
            'email' => $validated['email'],
            'created_at' => now(),
        ];

        if (Schema::hasColumn('newsletter_subscribers', 'active')) {
            $row['active'] = 1;
        }
        if (Schema::hasColumn('newsletter_subscribers', 'preference')) {
            $row['preference'] = 'all';
        }
        if (Schema::hasColumn('newsletter_subscribers', 'user_id') && Auth::check()) {
            $row['user_id'] = Auth::id();
        }
        if (Schema::hasColumn('newsletter_subscribers', 'subscribed_at')) {
            $row['subscribed_at'] = now();
        }

        DB::table('newsletter_subscribers')->insert($row);

        return back()->with('newsletterSuccess', 'Дякуємо за підписку!');
    }

    private function isActive(object $row): bool
    {
        if (isset($row->active)) {
            return (bool) $row->active;
        }

        if (isset($row->unsubscribed_at)) {
            return empty($row->unsubscribed_at);
        }

        return true;
    }

    private function reactivate(int $id): void
    {
        $update = [];

        if (Schema::hasColumn('newsletter_subscribers', 'active')) {
            $update['active'] = 1;
        }
        if (Schema::hasColumn('newsletter_subscribers', 'unsubscribed_at')) {
            $update['unsubscribed_at'] = null;
        }
        if (Schema::hasColumn('newsletter_subscribers', 'subscribed_at')) {
            $update['subscribed_at'] = now();
        }
        if (Schema::hasColumn('newsletter_subscribers', 'created_at')) {
            $update['created_at'] = now();
        }

        if (! empty($update)) {
            DB::table('newsletter_subscribers')->where('id', $id)->update($update);
        }
    }
}
