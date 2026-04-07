<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gmail_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gmail_account_id')->constrained('gmail_accounts')->cascadeOnDelete();
            $table->string('gmail_message_id');
            $table->string('thread_id')->nullable();
            $table->string('subject')->nullable();
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->text('snippet')->nullable();
            $table->longText('body_text')->nullable();
            $table->longText('body_html')->nullable();
            $table->json('labels')->nullable();
            $table->boolean('is_unread')->default(false);
            $table->timestamp('received_at')->nullable();
            $table->timestamps();

            $table->unique(['gmail_account_id', 'gmail_message_id']);
            $table->index(['gmail_account_id', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gmail_messages');
    }
};
