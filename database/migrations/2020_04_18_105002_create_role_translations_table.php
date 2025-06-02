<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('role_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')
                ->constrained('roles')
                ->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('display_name');
            $table->unique(['role_id', 'locale']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('role_translations');
    }
}
