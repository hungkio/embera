<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopRentalStatsTable extends Migration
{
    public function up()
    {
        Schema::create('shop_rental_stats', function (Blueprint $table) {
            $table->id();
            $table->string('shop_id')->index();
            $table->unsignedInteger('total_slots')->default(0);
            $table->unsignedInteger('renting_slots')->default(0);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shop_rental_stats');
    }

}
