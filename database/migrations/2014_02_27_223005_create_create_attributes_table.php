<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use WA\Database\Command\TablesRelationsAndIndexes;

class CreateCreateAttributesTable extends Migration {

    use TablesRelationsAndIndexes;

    protected $tableName = 'attributes';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attributes', function(Blueprint $table){

            $table->increments('id');

            $table->string('name');
                $table->string('meta')->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        $this->forceDropTable($this->tableName);
	}

}
