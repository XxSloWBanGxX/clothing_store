<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('favorite_shares')) {
            Schema::create('favorite_shares', function (Blueprint $table) {
                $table->id();
                $table->string('token', 64)->unique();
                $table->string('folder_name', 120);
                $table->json('product_ids');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('product_stock_alerts')) {
            Schema::create('product_stock_alerts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->string('email', 150);
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamp('notified_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->unique(['product_id', 'email']);
            });
        }

        if (! Schema::hasTable('favorite_price_alerts')) {
            Schema::create('favorite_price_alerts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->string('email', 150);
                $table->unsignedBigInteger('user_id')->nullable();
                $table->decimal('watched_price', 10, 2);
                $table->timestamp('notified_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->unique(['product_id', 'email']);
            });
        }

        if (! Schema::hasTable('newsletter_campaigns')) {
            Schema::create('newsletter_campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('subject', 200);
                $table->longText('body');
                $table->unsignedInteger('sent_count')->default(0);
                $table->unsignedInteger('failed_count')->default(0);
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'last_seen_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_seen_at')->nullable()->after('remember_token');
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (! Schema::hasColumn('orders', 'shipping_amount')) {
                    $table->decimal('shipping_amount', 10, 2)->default(0)->after('total_amount');
                }
            });

            try {
                DB::statement('ALTER TABLE orders MODIFY user_id BIGINT UNSIGNED NULL');
            } catch (\Throwable $e) {
                //
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_campaigns');
        Schema::dropIfExists('favorite_price_alerts');
        Schema::dropIfExists('product_stock_alerts');
        Schema::dropIfExists('favorite_shares');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'last_seen_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('last_seen_at');
            });
        }

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'shipping_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('shipping_amount');
            });
        }
    }
};
