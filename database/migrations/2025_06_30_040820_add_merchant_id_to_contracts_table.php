<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('contracts', function (Blueprint $table) {
        $table->unsignedBigInteger('merchant_id')->nullable()->after('contract_number');
    });
}

};
