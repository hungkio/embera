<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_turn_on_histories', function (Blueprint $table) {
            $table->id();
            $table->date('recorded_date')->index();
            $table->string('equip_id')->nullable();
            $table->string('code')->nullable();
            $table->string('shop_code')->nullable()->index();
            $table->enum('status', ['online', 'offline'])->default('offline')->index();
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();

            $table->unique(['recorded_date', 'equip_id'], 'device_turn_on_histories_date_equip_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_turn_on_histories');
    }
};
