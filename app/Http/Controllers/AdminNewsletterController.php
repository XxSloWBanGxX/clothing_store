<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminNewsletterController extends Controller
{
    public function index()
    {
        $subscribers = Schema::hasTable('newsletter_subscribers')
            ? DB::table('newsletter_subscribers')->orderByDesc('id')->get()
            : collect();

        $activeCount = $subscribers->filter(fn ($row) => $this->isActive($row))->count();

        return view('admin.newsletter', compact('subscribers', 'activeCount'));
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
