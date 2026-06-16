<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Mail\NewsletterCampaignMail;

class AdminNewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = Schema::hasTable('newsletter_subscribers')
            ? DB::table('newsletter_subscribers')->orderByDesc('id')
            : null;

        $subscribers = $query ? $query->get() : collect();

        $search = trim((string) $request->query('search', ''));
        $filter = $request->query('filter', 'all');

        $stats = [
            'total' => $subscribers->count(),
            'active' => 0,
            'unsubscribed' => 0,
            'recent' => 0,
        ];

        $recentSince = now()->subDays(30);

        $filtered = $subscribers->filter(function ($row) use (&$stats, $search, $filter, $recentSince) {
            $isActive = $this->isActive($row);
            if ($isActive) {
                $stats['active']++;
            } else {
                $stats['unsubscribed']++;
            }

            $subscribedAt = $row->subscribed_at ?? $row->created_at ?? null;
            if ($subscribedAt && \Illuminate\Support\Carbon::parse($subscribedAt)->gte($recentSince)) {
                $stats['recent']++;
            }

            if ($search !== '' && stripos($row->email ?? '', $search) === false) {
                return false;
            }

            if ($filter === 'active') {
                return $isActive;
            }
            if ($filter === 'unsubscribed') {
                return ! $isActive;
            }

            return true;
        });

        return view('admin.newsletter', compact('subscribers', 'filtered', 'stats', 'search', 'filter'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string', 'max:10000'],
        ], [
            'subject.required' => 'Введи тему листа',
            'body.required' => 'Введи текст розсилки',
        ]);

        if (! Schema::hasTable('newsletter_subscribers')) {
            return back()->withErrors(['send' => 'Таблиця підписників не знайдена']);
        }

        $recipients = DB::table('newsletter_subscribers')
            ->get()
            ->filter(fn ($row) => $this->isActive($row));

        if ($recipients->isEmpty()) {
            return back()->withErrors(['send' => 'Немає активних підписників для розсилки']);
        }

        $sent = 0;
        $failed = 0;

        foreach ($recipients as $subscriber) {
            try {
                Mail::to($subscriber->email)->send(new NewsletterCampaignMail(
                    $validated['subject'],
                    $validated['body'],
                ));
                $sent++;
            } catch (\Throwable $e) {
                $failed++;
            }
        }

        if (Schema::hasTable('newsletter_campaigns')) {
            $record = [
                'subject' => $validated['subject'],
                'body' => $validated['body'],
            ];

            if (Schema::hasColumn('newsletter_campaigns', 'sent_count')) {
                $record['sent_count'] = $sent;
            }
            if (Schema::hasColumn('newsletter_campaigns', 'failed_count')) {
                $record['failed_count'] = $failed;
            }
            if (Schema::hasColumn('newsletter_campaigns', 'sent_at')) {
                $record['sent_at'] = now();
            }
            if (Schema::hasColumn('newsletter_campaigns', 'created_at')) {
                $record['created_at'] = now();
            }

            DB::table('newsletter_campaigns')->insert($record);
        }

        return redirect('/admin/newsletter')->with(
            'status',
            'Розсилку «' . $validated['subject'] . '» надіслано: ' . $sent . ' листів' . ($failed > 0 ? ', помилок: ' . $failed : '')
        );
    }

    public function destroy($id)
    {
        DB::table('newsletter_subscribers')->where('id', $id)->delete();

        return redirect('/admin/newsletter')->with('status', 'Підписника видалено');
    }

    public function unsubscribe($id)
    {
        $update = [];

        if (Schema::hasColumn('newsletter_subscribers', 'active')) {
            $update['active'] = 0;
        }
        if (Schema::hasColumn('newsletter_subscribers', 'unsubscribed_at')) {
            $update['unsubscribed_at'] = now();
        }

        if (! empty($update)) {
            DB::table('newsletter_subscribers')->where('id', $id)->update($update);
        }

        return redirect('/admin/newsletter')->with('status', 'Підписника відписано');
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
}
