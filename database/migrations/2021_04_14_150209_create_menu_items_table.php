<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuItemsTable extends Migration
{
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->integer('menu_id');
            $table->unsignedInteger('parent_id')->nullable()->index();
            $table->string('name')->nullable();
            $table->bigInteger('type')->comment('1: category, 2: page');
            $table->tinyInteger('status')->comment('0: hide, 1: show');
            $table->bigInteger('item_id')->nullable();
            $table->text('item_content')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
         Schema::dropIfExists('menu_items');
    }
}
