<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('contract_no')->nullable();
            $table->string('file_name')->nullable();
            $table->json('devices')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('placed_time')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
         Schema::dropIfExists('contracts');
    }
};
