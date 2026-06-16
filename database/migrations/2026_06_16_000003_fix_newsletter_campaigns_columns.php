<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

            return;
        }

        Schema::table('newsletter_campaigns', function (Blueprint $table) {
            if (! Schema::hasColumn('newsletter_campaigns', 'subject')) {
                $table->string('subject', 200)->nullable();
            }
            if (! Schema::hasColumn('newsletter_campaigns', 'body')) {
                $table->longText('body')->nullable();
            }
            if (! Schema::hasColumn('newsletter_campaigns', 'sent_count')) {
                $table->unsignedInteger('sent_count')->default(0);
            }
            if (! Schema::hasColumn('newsletter_campaigns', 'failed_count')) {
                $table->unsignedInteger('failed_count')->default(0);
            }
            if (! Schema::hasColumn('newsletter_campaigns', 'sent_at')) {
                $table->timestamp('sent_at')->nullable();
            }
            if (! Schema::hasColumn('newsletter_campaigns', 'created_at')) {
                $table->timestamp('created_at')->useCurrent();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('newsletter_campaigns')) {
            return;
        }

        Schema::table('newsletter_campaigns', function (Blueprint $table) {
            foreach (['sent_count', 'failed_count', 'sent_at'] as $column) {
                if (Schema::hasColumn('newsletter_campaigns', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
