<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{

    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'pages';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function(Blueprint $table){

            $table->increments('id');
            $table->string('title');
            $table->string('section');
            $table->text('content');
            $table->boolean('active')->default(0);
            $table->integer('roleId');
            $table->integer('companyId');
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
        Schema::drop($this->tableName);
    }
}
