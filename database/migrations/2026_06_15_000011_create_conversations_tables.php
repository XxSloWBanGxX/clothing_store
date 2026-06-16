<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('guest_name', 150)->nullable();
                $table->string('guest_email', 150)->nullable();
                $table->string('subject', 200)->default('Звернення в підтримку');
                $table->string('status', 20)->default('open');
                $table->timestamp('last_message_at')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrent();
            });
        }

        if (! Schema::hasTable('conversation_messages')) {
            Schema::create('conversation_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('conversation_id')->index();
                $table->string('sender_role', 10);
                $table->unsignedBigInteger('sender_user_id')->nullable();
                $table->text('body');
                $table->timestamp('read_at')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_messages');
        Schema::dropIfExists('conversations');
    }
};
