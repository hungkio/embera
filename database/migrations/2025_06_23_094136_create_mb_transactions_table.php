<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mb_transactions', function (Blueprint $table) {
            $table->id();

            // Giao dịch nạp
            $table->string('code_in')->nullable();           // Mã giao dịch nạp
            $table->timestamp('date_in')->nullable();        // Thời gian giao dịch nạp
            $table->string('ft_code_in')->nullable();        // FT nạp
            $table->decimal('amount_in', 15, 2)->nullable(); // Số tiền nạp

            // Giao dịch hoàn
            $table->string('code_out')->nullable();           // Mã giao dịch gốc (code_in)
            $table->timestamp('date_out')->nullable();        // Thời gian hoàn tiền
            $table->string('ft_code_out')->nullable();        // FT hoàn
            $table->decimal('amount_out', 15, 2)->nullable(); // Số tiền hoàn

            // Kết quả tính toán
            $table->decimal('revenue', 15, 2)->nullable(); // Doanh thu = nạp - hoàn

            $table->timestamps();
        });
    }

    public function down()
    {
         Schema::dropIfExists('mb_transactions');
    }
};
