<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogSearchsTable extends Migration
{
    public function up()
    {
        Schema::create('log_searchs', function (Blueprint $table) {
            $table->id();
            $table->string('key_word', 255);
            $table->timestamps();
        });
    }

    public function down()
    {
         Schema::dropIfExists('log_searchs');
    }
}
