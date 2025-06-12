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
            $table->string('contract_number')->nullable();
            $table->date('sign_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->string('status')->nullable();
            $table->integer('expired_time')->nullable();
            $table->string('bank_info')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('title')->nullable();
            $table->string('ceo_sign')->nullable();
            $table->string('location')->nullable();
            $table->text('note')->nullable();
            $table->string('upload')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'contract_number',
                'sign_date',
                'expired_date',
                'status',
                'expired_time',
                'bank_info',
                'email',
                'phone',
                'admin_id',
                'title',
                'ceo_sign',
                'location',
                'note',
                'upload',
                'download_count',
            ]);
        });
    }
};
