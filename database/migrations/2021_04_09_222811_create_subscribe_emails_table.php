<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribeEmailsTable extends Migration
{
    public function up()
    {
        Schema::create('subscribe_emails', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
         Schema::dropIfExists('subscribe_emails');
    }
}
