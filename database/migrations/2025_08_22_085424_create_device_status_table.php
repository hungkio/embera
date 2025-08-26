<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceStatusTable extends Migration
{
    public function up()
    {
        Schema::create('device_status', function (Blueprint $table) {
            $table->id();
            $table->string('equip_id')->unique();
            $table->enum('status', ['online', 'offline'])->default('offline');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_status');
    }
}
