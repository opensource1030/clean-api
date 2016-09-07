<?php

use Illuminate\Database\Migrations\Migration;
use WA\Database\Command\TablesRelationsAndIndexes;

class ModifyDeviceTable extends Migration
{
    use TablesRelationsAndIndexes;

    protected $tableName = 'devices';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function($table){
            $table->dropForeign('devices_carrierid_foreign');
            $table->dropColumn('carrierId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tableName, function($table){
            $table->integer('carrierId')->unsigned()->nullable();
            $table->foreign('carrierId')->references('id')->on('carriers');
        });
    }
}
