<?php

namespace App\Http\Controllers;

use App\Services\SiteSettings;
use Illuminate\Http\Request;

class AdminSiteController extends Controller
{
    public function settings(Request $request)
    {
        $settings = SiteSettings::all();
        $activeTab = $request->query('tab', 'brand');

        $allowedTabs = ['brand', 'contacts', 'homepage', 'footer', 'shop'];
        if (! in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'brand';
        }

        return view('admin.settings', compact('settings', 'activeTab'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => ['nullable', 'string', 'max:80'],
            'brand_lead' => ['nullable', 'string', 'max:40'],
            'brand_accent' => ['nullable', 'string', 'max:40'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'contact_location' => ['nullable', 'string', 'max:120'],
            'instagram_url' => ['nullable', 'url', 'max:500'],
            'instagram_handle' => ['nullable', 'string', 'max:80'],
            'footer_description' => ['nullable', 'string', 'max:2000'],
            'footer_strip_text' => ['nullable', 'string', 'max:200'],
            'footer_strip_link_text' => ['nullable', 'string', 'max:120'],
            'footer_strip_link_url' => ['nullable', 'string', 'max:500'],
            'delivery_carriers' => ['nullable', 'string', 'max:500'],
            'trust_payment_text' => ['nullable', 'string', 'max:200'],
            'shipping_info' => ['nullable', 'string', 'max:1000'],
            'returns_info' => ['nullable', 'string', 'max:1000'],
            'hero_badge' => ['nullable', 'string', 'max:80'],
            'hero_title' => ['nullable', 'string', 'max:500'],
            'hero_text' => ['nullable', 'string', 'max:2000'],
            'hero_btn1_text' => ['nullable', 'string', 'max:80'],
            'hero_btn1_url' => ['nullable', 'string', 'max:500'],
            'hero_btn2_text' => ['nullable', 'string', 'max:80'],
            'hero_btn2_url' => ['nullable', 'string', 'max:500'],
            'hero_stat1_value' => ['nullable', 'string', 'max:40'],
            'hero_stat1_label' => ['nullable', 'string', 'max:80'],
            'hero_stat2_value' => ['nullable', 'string', 'max:40'],
            'hero_stat2_label' => ['nullable', 'string', 'max:80'],
            'hero_stat3_value' => ['nullable', 'string', 'max:40'],
            'hero_stat3_label' => ['nullable', 'string', 'max:80'],
            'feature1_title' => ['nullable', 'string', 'max:120'],
            'feature1_text' => ['nullable', 'string', 'max:500'],
            'feature2_title' => ['nullable', 'string', 'max:120'],
            'feature2_text' => ['nullable', 'string', 'max:500'],
            'feature3_title' => ['nullable', 'string', 'max:120'],
            'feature3_text' => ['nullable', 'string', 'max:500'],
            'banner_label' => ['nullable', 'string', 'max:80'],
            'banner_title' => ['nullable', 'string', 'max:200'],
            'banner_text' => ['nullable', 'string', 'max:1000'],
            'banner_btn_text' => ['nullable', 'string', 'max:80'],
            'banner_btn_url' => ['nullable', 'string', 'max:500'],
            'new_products_days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ], [
            'contact_email.email' => 'Вкажи коректний email',
            'instagram_url.url' => 'Instagram URL має починатися з https://',
            'new_products_days.min' => 'Мінімум 1 день для розділу «Новинки»',
            'new_products_days.max' => 'Максимум 365 днів',
        ]);

        $keys = array_keys(SiteSettings::defaults());
        $data = $request->only($keys);
        $data['reviews_moderation'] = $request->boolean('reviews_moderation') ? '1' : '0';

        foreach ($keys as $key) {
            if (! array_key_exists($key, $data)) {
                $data[$key] = SiteSettings::get($key);
            }
        }

        SiteSettings::setMany($data);

        $tab = $request->input('_active_tab', 'brand');

        return redirect('/admin/settings?tab=' . urlencode($tab))->with('status', 'Налаштування збережено');
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
