<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'delivery_city')) {
                $table->string('delivery_city', 100)->nullable()->after('avatar');
            }
            if (! Schema::hasColumn('users', 'delivery_branch')) {
                $table->string('delivery_branch', 255)->nullable()->after('delivery_city');
            }
            if (! Schema::hasColumn('users', 'bonus_points')) {
                $table->unsignedInteger('bonus_points')->default(0)->after('delivery_branch');
            }
        });

        if (! Schema::hasTable('user_promocodes')) {
            Schema::create('user_promocodes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('code', 32);
                $table->string('title', 120);
                $table->unsignedTinyInteger('discount_percent')->default(10);
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('used_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('bonus_history')) {
            Schema::create('bonus_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->integer('points');
                $table->string('type', 30)->default('accrual');
                $table->string('description', 255);
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bonus_history');
        Schema::dropIfExists('user_promocodes');

        Schema::table('users', function (Blueprint $table) {
            foreach (['delivery_city', 'delivery_branch', 'bonus_points'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
