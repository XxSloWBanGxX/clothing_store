<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('author_name', 150);
                $table->unsignedTinyInteger('rating')->default(5);
                $table->text('comment');
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->index('product_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
