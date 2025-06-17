<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('merchants')) {
            Schema::create('merchants', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('username')->unique();
                $table->string('password');
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->boolean('is_deleted')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('merchants');
    }
};
