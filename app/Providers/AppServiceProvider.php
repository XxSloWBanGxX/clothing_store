<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
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
            ]);
        });
    }
}
