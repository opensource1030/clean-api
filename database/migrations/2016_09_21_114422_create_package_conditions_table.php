<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageConditionsTable extends Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'package_conditions';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists($this->tableName);

        Schema::create(
            $this->tableName,
            function ($table) {
                $table->increments('id');
                $table->integer('packageId')->unsigned();
                $table->integer('conditionsId')->unsigned();

                $table->nullableTimestamps();
            }
        );

        Schema::table(
            $this->tableName, 
            function($table) {
                $table->foreign('packageId')->references('id')->on('packages')->onDelete('cascade');
                $table->foreign('conditionsId')->references('id')->on('conditions')->onDelete('cascade');
            }
        );
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
