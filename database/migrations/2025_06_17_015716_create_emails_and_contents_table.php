<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('to')->index();
            $table->foreignId('merchant_id')->constrained('merchants')->onDelete('cascade');
            $table->string('status')->default('pending'); // Ví dụ: pending, sent, failed
            $table->timestamps();
        });

        // Tạo bảng email_contents
        Schema::create('email_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_id')->constrained('emails')->onDelete('cascade');
            $table->longText('text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_contents');
        Schema::dropIfExists('emails');
    }
};
