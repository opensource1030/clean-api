<?php

use Illuminate\Database\Migrations\Migration;


class CreateUserUdlsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'employee_udls',
            function ( $table) {
                $table->increments('id');

                $table->integer('employeeId', false, true);
                $table->integer('udlValueId', false, true);
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
        Schema::drop('employee_udls');
    }

}
