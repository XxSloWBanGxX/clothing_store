<?php

namespace App\Console\Commands;

use App\Services\PricingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class SendShopAlerts extends Command
{
    protected $signature = 'shop:send-alerts';

    protected $description = 'Send stock and price-drop alert emails';

    public function handle(PricingService $pricing): int
    {
        $stockSent = $this->sendStockAlerts($pricing);
        $priceSent = $this->sendPriceAlerts($pricing);

        $this->info("Stock alerts: {$stockSent}, price alerts: {$priceSent}");

        return self::SUCCESS;
    }

    private function sendStockAlerts(PricingService $pricing): int
    {
        if (! Schema::hasTable('product_stock_alerts')) {
            return 0;
        }

        $sent = 0;
        $rows = DB::table('product_stock_alerts')->whereNull('notified_at')->get();

        foreach ($rows as $row) {
            $product = DB::table('products')->where('id', $row->product_id)->first();
            if (! $product || (int) $product->stock <= 0) {
                continue;
            }

            $url = url('/product/' . $product->id);

            try {
                Mail::raw(
                    "Товар «{$product->name}» знову в наявності!\n\nПереглянути: {$url}",
                    fn ($m) => $m->to($row->email)->subject('Товар зʼявився в наявності — CLOTHSTORE')
                );
                DB::table('product_stock_alerts')->where('id', $row->id)->update(['notified_at' => now()]);
                $sent++;
            } catch (\Throwable $e) {
                //
            }
        }

        return $sent;
    }

    private function sendPriceAlerts(PricingService $pricing): int
    {
        if (! Schema::hasTable('favorite_price_alerts')) {
            return 0;
        }

        $sent = 0;
        $rows = DB::table('favorite_price_alerts')->whereNull('notified_at')->get();

        foreach ($rows as $row) {
            $product = DB::table('products')->where('id', $row->product_id)->first();
            if (! $product) {
                continue;
            }

            $current = $pricing->getEffectivePrice($product);
            if ($current >= (float) $row->watched_price) {
                continue;
            }

            $url = url('/product/' . $product->id);
            $price = number_format($current, 0, '.', ' ');

            try {
                Mail::raw(
                    "Ціна на «{$product->name}» знизилась до {$price} грн!\n\nПереглянути: {$url}",
                    fn ($m) => $m->to($row->email)->subject('Знижка на обране — CLOTHSTORE')
                );
                DB::table('favorite_price_alerts')->where('id', $row->id)->update(['notified_at' => now()]);
                $sent++;
            } catch (\Throwable $e) {
                //
            }
        }

        return $sent;
    }
}
