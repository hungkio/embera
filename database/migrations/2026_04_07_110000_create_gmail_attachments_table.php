<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gmail_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gmail_message_id')->constrained('gmail_messages')->cascadeOnDelete();
            $table->string('filename');
            $table->string('mime_type')->nullable();
            $table->string('gmail_attachment_id')->nullable();
            $table->string('storage_disk')->default('local');
            $table->string('storage_path');
            $table->unsignedBigInteger('size')->default(0);
            $table->timestamps();

            $table->unique(['gmail_message_id', 'filename']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gmail_attachments');
    }
};
