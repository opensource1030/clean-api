<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
        Schema::table($this->tableName, function(Blueprint $table){
            $table->dropColumn('identification');
            $table->dropColumn('externalId');
            $table->dropColumn('statusId');
            $table->dropColumn('carrierId');
            $table->dropColumn('syncId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tableName, function(Blueprint $table){
            $table->string('identification')->unique()->nullable();
            $table->integer('externalId')->unsigned();
            $table->integer('statusId')->unsigned()->nullable();
            $table->integer('carrierId')->unsigned()->nullable();
            $table->integer('syncId')->unsigned()->nullable();
        });
    }
}
