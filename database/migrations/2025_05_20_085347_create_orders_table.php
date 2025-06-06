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
            $table->string('payment_id')->nullable();
            $table->string('payment_failure_reason')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('rental_equipment_id')->nullable();
            $table->string('return_equipment_id')->nullable();
            $table->timestamp('rental_time')->nullable();
            $table->timestamp('return_time')->nullable();
            $table->string('rental_shop_id')->nullable();
            $table->string('rental_shop')->nullable();
            $table->string('rental_shop_type')->nullable();
            $table->string('rental_shop_address')->nullable();
            $table->string('return_shop')->nullable();
            $table->string('duration_of_use')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('order_amount', 15, 2)->nullable();
            $table->decimal('fees', 15, 2)->nullable();
            $table->string('order_status')->nullable();
            $table->string('orders_belong_to_merchants')->nullable();
            $table->string('merchant_id')->nullable();
            $table->string('merchant_name')->nullable();
            $table->string('service_provider_id')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('employee_name')->nullable();
            $table->string('order_source')->nullable();
            $table->timestamp('payment_time')->nullable();
            $table->string('payment_channels')->nullable();
            $table->string('refund_status')->nullable();
            $table->decimal('refund_amount', 15, 2)->nullable();
            $table->decimal('refund_fee', 15, 2)->nullable();
            $table->decimal('agent_share_ratio', 5, 3)->nullable();
            $table->decimal('franchisee_share_ratio', 5, 3)->nullable();
            $table->decimal('service_provider_share_ratio', 5, 3)->nullable();
            $table->decimal('merchant_share_ratio', 5, 3)->nullable();
            $table->string('charging_strategy')->nullable();
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
