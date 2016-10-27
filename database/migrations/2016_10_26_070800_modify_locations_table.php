<?php

use Illuminate\Database\Migrations\Migration;

class ModifyLocationsTable extends Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'locations';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table($this->tableName, function ($table) {
            $table->string('country')->default('');
            $table->string('city')->default('');
            $table->string('zipCode')->default('');
            $table->string('address')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
