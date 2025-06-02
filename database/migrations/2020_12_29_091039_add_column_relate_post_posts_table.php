<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnRelatePostPostsTable extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->json('related_posts')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('related_posts');
    }
}
