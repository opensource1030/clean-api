<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUdlsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'udls',
            function (Blueprint $table) {
                $table->increments('id');

                $table->integer('companyId', false, true);
                $table->string('name');
                $table->string('label');
                $table->string('legacyUdlField', 50)->nullable();

                $table->unique(['companyId','name']);

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
        Schema::drop('udls');
    }

}
