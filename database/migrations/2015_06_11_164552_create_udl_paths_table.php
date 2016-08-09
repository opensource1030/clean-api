<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUdlPathsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('udl_value_paths', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('udlPath');
            $table->integer('udlId');
            $table->integer('externalId');
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
		Schema::drop('udl_value_paths');
	}

}