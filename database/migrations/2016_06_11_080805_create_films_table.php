<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('films', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title_viet')->nullable();
            $table->string('title_english')->nullable();
            $table->text('description')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('fb_id')->nullable();
            $table->string('thumbnail_id')->nullable();
            $table->string('play_time')->nullable();
            $table->integer('year')->nullable();
            $table->string('country')->nullable();
            $table->integer('type')->nullable()->default(1);
            $table->string('crawler_at')->unique();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('films');
    }
}
