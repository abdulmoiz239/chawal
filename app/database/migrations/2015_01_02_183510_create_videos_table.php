<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('videos', function(Blueprint $table)
		{
			$table->increments('id');
                        $table->string('path');
                        $table->string('youtube_url');
                        $table->boolean('featured');
                        $table->integer('views');
                        $table->string('name');
                        $table->string('ext');
                        $table->string('image');
                        $table->integer('likes');
                        $table->integer('dislikes');
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
		Schema::drop('videos');
	}

}
