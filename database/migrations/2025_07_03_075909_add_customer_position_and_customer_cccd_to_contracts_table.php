<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('customer_position')->nullable()->after('customer_name');
            $table->string('customer_cccd')->nullable()->after('customer_position');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('customer_cccd');
            $table->dropColumn('customer_position');
        });
    }
};
