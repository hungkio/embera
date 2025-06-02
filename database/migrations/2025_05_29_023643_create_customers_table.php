<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('shop_type')->nullable();
            $table->string('shop_name')->nullable();
            $table->string('merchant')->nullable();
            $table->string('contact')->nullable();
            $table->string('address')->nullable();
            $table->bigInteger('admin_id')->nullable();
            $table->integer('share_rate')->nullable();
            $table->string('ward')->nullable();
            $table->string('district')->nullable();
            $table->string('province')->nullable();
            $table->string('region')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
         Schema::dropIfExists('customers');
    }
};
