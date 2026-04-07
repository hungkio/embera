<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gmail_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->string('email');
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->string('token_type')->nullable();
            $table->json('scopes')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique('admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gmail_accounts');
    }
};
