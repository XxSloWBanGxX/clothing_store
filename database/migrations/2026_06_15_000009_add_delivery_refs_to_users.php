<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'delivery_carrier')) {
                $table->string('delivery_carrier', 30)->nullable()->after('delivery_branch');
            }
            if (! Schema::hasColumn('users', 'delivery_city_ref')) {
                $table->string('delivery_city_ref', 64)->nullable()->after('delivery_carrier');
            }
            if (! Schema::hasColumn('users', 'delivery_branch_ref')) {
                $table->string('delivery_branch_ref', 64)->nullable()->after('delivery_city_ref');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['delivery_carrier', 'delivery_city_ref', 'delivery_branch_ref'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
