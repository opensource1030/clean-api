<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyServicesTable extends Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'services';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table(
            $this->tableName, function ($table) {
                $table->integer('carrierId')->unsigned()->nullable();
                $table->string('status')->nullable();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->foreign('carrierId')->references('id')->on('carriers');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
