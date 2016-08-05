<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUdlValuesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()

    {
        Schema::create(
            'udl_values',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->integer('udlId', false, true)->nullable();
                $table->integer('externalId')->nullable();

//                $table->unique(['name','udlId']);
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
        Schema::drop('udl_values');
    }

}
