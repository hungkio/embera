<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropCollumnCreatedByPagesTable extends Migration
{
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }

    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->unsignedInteger('created_by');
        });
    }
}
