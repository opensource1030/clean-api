<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use WA\Database\Command\TablesRelationsAndIndexes;

class CreateAssetTypesTable extends Migration
{

    use TablesRelationsAndIndexes;

    protected $tableName = 'asset_types';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            $this->tableName,
            function (Blueprint $table) {
            $table->increments('id');
                $table->string('name');
                $table->string('description');
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
