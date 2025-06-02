<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxonsTable extends Migration
{
    public function up()
    {
        Schema::create('taxons', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('parent_id')->nullable()->index();
            $table->unsignedInteger('taxonomy_id')->index();
            $table->string('name');
            $table->string('slug')->index();
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->integer('order_column');
            $table->index(['name', 'parent_id', 'taxonomy_id']);
            $table->timestamps();
        });
        Schema::create('taxonables', function (Blueprint $table) {
            $table->foreignId('taxon_id');
            $table->morphs('taxonable');
            $table->unique(['taxon_id', 'taxonable_id', 'taxonable_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('taxons');
        Schema::dropIfExists('taxonables');
    }
}
