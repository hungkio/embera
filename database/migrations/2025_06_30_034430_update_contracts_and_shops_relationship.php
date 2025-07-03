<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateContractsAndShopsRelationship extends Migration
{
    public function up()
    {
        // Drop shop_id if it exists (ignore foreign key)
        Schema::table('contracts', function (Blueprint $table) {
            if (Schema::hasColumn('contracts', 'shop_id')) {
                $table->dropColumn('shop_id'); // safest fallback if FK never existed
            }
        });

        // Add contract_id to shops
        Schema::table('shops', function (Blueprint $table) {
            if (!Schema::hasColumn('shops', 'contract_id')) {
                $table->unsignedBigInteger('contract_id')->nullable()->after('merchant_id');
            }
        });
    }

    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropForeign(['contract_id']);
            $table->dropColumn('contract_id');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id')->nullable();
        });
    }
}
