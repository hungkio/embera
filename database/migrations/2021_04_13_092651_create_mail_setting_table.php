<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailSettingTable extends Migration
{
    public function up()
    {
        Schema::create('mail_settings', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 255);
            $table->string('name', 255);
            $table->longText('value');
            $table->timestamps();
        });
    }

    public function down()
    {
         Schema::dropIfExists('mail_settings');
    }
}
