<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('title_image')->unsigned()->default(1);
            $table->string('title',100);
            $table->text('description')->nullable();
            $table->string('path',255)->nullable();
            $table->string('slug',255);
            $table->boolean('active')->default(0);
            $table->timestamps();

            $table->foreign('title_image')->references('id')->on('images');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stories');
    }
}
