<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('merchant_share_logs', function (Blueprint $table) {
            $table->string('status')->after('share_money');
        });
    }

    public function down()
    {
        Schema::table('merchant_share_logs', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
    }

};
