<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('contracts')) {
            Schema::create('contracts', function (Blueprint $table) {
                $table->id();
                $table->string('contract_number')->nullable();
                $table->date('sign_date')->nullable();
                $table->date('expired_date')->nullable();
                $table->string('status')->default('chưa_ký');
                $table->string('expired_time')->nullable();
                $table->string('bank_info')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->unsignedBigInteger('shop_id')->nullable();
                $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
                $table->string('title')->nullable();
                $table->string('ceo_sign')->nullable();
                $table->string('location')->nullable();
                $table->text('note')->nullable();
                $table->string('upload')->nullable();
                $table->unsignedInteger('download_count')->default(0);
                $table->boolean('is_deleted')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
};
