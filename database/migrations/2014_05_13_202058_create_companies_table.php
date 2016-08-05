<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use WA\Database\Command\TablesRelationsAndIndexes;

class CreateCompaniesTable extends Migration
{
    use TablesRelationsAndIndexes;

    protected $tableName = 'companies';

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
                $table->string('label');
                $table->boolean('active')->default(0);
                $table->string('udlpath')->nullable();
                $table->boolean('isCensus')->nullable();
                $table->string('udlPathRule')->nullable();
                $table->string('assetPath');
                $table->nullableTimestamps();
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
