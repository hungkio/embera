<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReplaceDeletedAtWithIsDeletedInContractsTable extends Migration
{
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('deleted_at'); // Xoá cột soft delete
            $table->boolean('is_deleted')->default(0)->after('note'); // Thêm cột boolean
        });
    }

    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
            $table->softDeletes(); // Khôi phục lại nếu rollback
        });
    }
}
