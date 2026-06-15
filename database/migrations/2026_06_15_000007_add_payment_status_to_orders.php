<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'payment_status')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('payment_status', 20)->default('pending')->after('payment_method');
            });
        }

        if (! Schema::hasColumn('orders', 'card_last4')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('card_last4', 4)->nullable()->after('payment_status');
            });
        }

        if (! Schema::hasColumn('orders', 'payment_reference')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('payment_reference', 64)->nullable()->after('card_last4');
            });
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = ['payment_status', 'card_last4', 'payment_reference'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
