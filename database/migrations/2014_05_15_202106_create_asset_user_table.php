<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssetUserTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create(
            'employee_assets',
            function (Blueprint $table)
		{
			$table->increments('id');
            $table->integer('assetId', false, true)->index();
            $table->integer('employeeId', false, true)->index();
			$table->nullableTimestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('employee_assets');
	}

}