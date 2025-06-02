<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('link')->nullable();
            $table->string('section');
            $table->string('status');
            $table->tinyInteger('position')->default(1);
            $table->unsignedInteger('user_id');
            $table->timestamps();
        });
    }

    public function down()
    {
         Schema::dropIfExists('banners');
    }
}
