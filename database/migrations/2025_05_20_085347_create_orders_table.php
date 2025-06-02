<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('payment_order_id')->nullable();
            $table->string('reason_for_payment_failure')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('rent_out_from')->nullable();
            $table->string('return_to')->nullable();
            $table->timestamp('when_to_rent')->nullable();
            $table->timestamp('when_to_return')->nullable();
            $table->string('merchant_id_rent_out_from')->nullable();
            $table->string('merchant_rent_out_from')->nullable();
            $table->string('merchant_return_to')->nullable();
            $table->string('renting_time')->nullable();
            $table->string('order_bills')->nullable();
            $table->decimal('order_bills_vnd', 15, 2)->nullable();
            $table->string('commission_fees')->nullable();
            $table->decimal('commission_fees_vnd', 15, 2)->nullable();
            $table->string('status_of_order')->nullable();
            $table->string('order_belongs_to')->nullable();
            $table->string('merchant_id')->nullable();
            $table->string('name_of_merchant')->nullable();
            $table->string('staff_id')->nullable();
            $table->string('staff_name')->nullable();
            $table->string('order_comes_from')->nullable();
            $table->timestamp('when_to_pay')->nullable();
            $table->string('payment_channel')->nullable();
            $table->string('status_of_refund')->nullable();
            $table->integer('refund')->nullable();
            $table->decimal('commission_of_refunds', 15, 2)->nullable();
            $table->decimal('profit_sharing_to_dealer', 5, 3)->nullable();
            $table->decimal('revenue_to_dealer', 15, 2)->nullable();
            $table->decimal('revenue_to_merchant', 15, 2)->nullable();
            $table->string('billing_strategy')->nullable();
            $table->string('shop_name')->nullable();
            $table->string('shop_type')->nullable();
            $table->string('location')->nullable();

            // Extra for derived filters
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('area')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
         Schema::dropIfExists('orders');
    }
};
