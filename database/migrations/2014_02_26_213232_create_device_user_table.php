<?php

use Illuminate\Database\Migrations\Migration;
use WA\Database\Command\TablesRelationsAndIndexes;

class CreateDeviceUserTable extends Migration
{
    use TablesRelationsAndIndexes;

    protected $tableName = 'device_users';

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

        });

        Schema::table(
            $this->tableName, 
            function($table) {
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
                //$table->dropForeign('deviceId');
        });
        /*
        Schema::table(
            $this->tableName, 
            function ( $table) {
                //$table->dropForeign('employeeId');
        });
        */

        $this->forceDropTable($this->tableName);
    }

}
