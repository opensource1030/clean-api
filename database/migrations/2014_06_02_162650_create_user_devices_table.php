<?php

use Illuminate\Database\Migrations\Migration;

use WA\Database\Command\TablesRelationsAndIndexes;

class CreateUserDevicesTable extends Migration {

    use TablesRelationsAndIndexes;

    protected $tableName = 'employee_devices';

    protected $foreignColumns = [
        'employeeId',
        'deviceId',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            $this->tableName,
            function ( $table) {
                $table->increments('id');
                $table->integer('employeeId')->unsigned();
                $table->integer('deviceId')->unsigned();
    
                $table->nullableTimestamps();
            }
        );

        Schema::table(
            $this->tableName, 
            function($table) {
                $table->foreign('employeeId')->references('id')->on('users');
                $table->foreign('deviceId')->references('id')->on('devices');
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
        Schema::table(
            $this->tableName, 
            function ( $table) {
                //$table->dropForeign('employeeId');
                //$table->dropForeign('deviceId');
        });
        $this->forceDropTable($this->tableName);
    }

}
