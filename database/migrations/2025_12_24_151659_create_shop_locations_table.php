<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopLocationsTable extends Migration
{
    public function up()
    {
        Schema::create('shop_locations', function (Blueprint $table) {
            $table->id();

            $table->string('shop_id')->unique();
            $table->string('shop_name')->nullable();

            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shop_locations');
    }
}
