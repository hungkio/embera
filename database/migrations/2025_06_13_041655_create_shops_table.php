<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tạo bảng shops với tất cả cột nếu chưa tồn tại
        if (!Schema::hasTable('shops')) {
            Schema::create('shops', function (Blueprint $table) {
                $table->id();
                $table->string('shop_name');
                $table->string('address');
                $table->string('shop_type')->nullable();
                $table->string('contact_phone')->nullable();
                $table->string('strategy')->nullable();
                $table->string('area')->nullable();
                $table->string('city')->nullable();
                $table->string('region')->nullable();
                $table->foreignId('merchant_id')->constrained('merchants')->onDelete('cascade');
                $table->boolean('is_deleted')->default(false);
                $table->decimal('share_rate', 10, 2)->nullable();
                $table->json('device_json')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        // Xóa bảng shops nếu tồn tại
        if (Schema::hasTable('shops')) {
            Schema::dropIfExists('shops');
        }
    }
};
