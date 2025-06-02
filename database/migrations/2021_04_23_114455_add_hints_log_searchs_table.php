<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHintsLogSearchsTable extends Migration
{
    public function up()
    {
        Schema::table('log_searchs', function (Blueprint $table) {
            $table->integer('hits')->default(0);
        });
    }

    public function down()
    {
        Schema::table('log_searchs', function (Blueprint $table) {
            $table->dropColumn('hits');
        });
    }
}
