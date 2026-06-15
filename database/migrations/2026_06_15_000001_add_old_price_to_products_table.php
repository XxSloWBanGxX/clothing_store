<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'old_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('old_price', 10, 2)->nullable()->after('price');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'old_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('old_price');
            });
        }
    }
};
