<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinsTable extends Migration
{
    public function up()
    {
        Schema::create('pins', function (Blueprint $table) {
            $table->id();
            $table->string('imei')->unique();
            $table->string('serial_number')->unique();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pins');
    }
}
