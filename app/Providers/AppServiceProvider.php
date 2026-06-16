<?php

namespace App\Providers;

use App\Services\SiteSettings;
use App\Services\PricingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $navCategories = [];

            try {
                $navCategories = DB::table('categories')->orderBy('name')->get();
            } catch (\Throwable $e) {
                $navCategories = collect();
            }

            $cart = session('cart', []);
            $cartCount = 0;
            foreach ($cart as $item) {
                $cartCount += (int) ($item['quantity'] ?? 0);
            }

            $favFolders = session('favorite_folders', ['Обране' => []]);
            $favCount = 0;
            foreach ($favFolders as $ids) {
                $favCount += count($ids);
            }

            $view->with([
                'navCategories' => $navCategories,
                'cartCount' => $cartCount,
                'favCount' => $favCount,
                'site' => SiteSettings::all(),
                'activeSaleBanner' => app(PricingService::class)->getBannerSale(),
            ]);
        });

        View::composer('admin.*', function ($view) {
            $adminNav = [
                'orders_new' => 0,
                'support_new' => 0,
            ];

            try {
                $adminNav['orders_new'] = (int) DB::table('orders')->where('status', 'new')->count();
                $adminNav['support_new'] = (int) DB::table('support_messages')->where('status', '!=', 'resolved')->count();

                if (Schema::hasTable('conversation_messages')) {
                    $adminNav['messages_unread'] = (int) DB::table('conversation_messages')
                        ->where('sender_role', 'user')
                        ->whereNull('read_at')
                        ->count();
                }

                if (Schema::hasTable('reviews') && Schema::hasColumn('reviews', 'is_approved')) {
                    $adminNav['reviews_pending'] = (int) DB::table('reviews')->where('is_approved', 0)->count();
                }
            } catch (\Throwable $e) {
                //
            }

            $view->with('adminNav', $adminNav);
        });
    }
}
