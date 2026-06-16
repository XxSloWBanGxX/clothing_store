<?php

namespace App\Http\Controllers;

use App\Services\SiteSettings;
use Illuminate\Http\Request;

class AdminSiteController extends Controller
{
    public function settings()
    {
        $settings = SiteSettings::all();

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $keys = array_keys(SiteSettings::defaults());

        $data = $request->only($keys);
        $data['reviews_moderation'] = $request->boolean('reviews_moderation') ? '1' : '0';

        SiteSettings::setMany($data);

        return redirect('/admin/settings')->with('status', 'Налаштування збережено');
    }

    public function pages()
    {
        $pages = \Illuminate\Support\Facades\DB::table('cms_pages')
            ->orderBy('slug')
            ->get();

        return view('admin.pages', compact('pages'));
    }

    public function editPage($slug)
    {
        $page = \Illuminate\Support\Facades\DB::table('cms_pages')->where('slug', $slug)->first();

        if (! $page) {
            abort(404);
        }

        return view('admin.page-edit', compact('page'));
    }

    public function updatePage(Request $request, $slug)
    {
        $page = \Illuminate\Support\Facades\DB::table('cms_pages')->where('slug', $slug)->first();

        if (! $page) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
        ], [
            'title.required' => 'Введи заголовок сторінки',
        ]);

        \Illuminate\Support\Facades\DB::table('cms_pages')->where('slug', $slug)->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? '',
            'content' => $validated['content'] ?? '',
            'is_published' => $request->boolean('is_published') ? 1 : 0,
            'updated_at' => now(),
        ]);

        return redirect('/admin/pages')->with('status', 'Сторінку оновлено');
    }
}
