<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantShareLogsTable extends Migration
{
    public function up()
    {
        Schema::create('merchant_share_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id');
            $table->smallInteger('year');
            $table->tinyInteger('month');
            $table->string('contract_no')->nullable();
            $table->string('customer_name')->nullable();
            $table->date('date');
            $table->integer('number_of_order');
            $table->decimal('share_percent', 5, 2);
            $table->decimal('total',         15, 0)->default(0);
            $table->decimal('share_money',   15, 0)->default(0);
            $table->string('type',       20)->default('email');
            $table->string('share_type', 20)->default('percentage');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('merchant_share_logs');
    }
}
