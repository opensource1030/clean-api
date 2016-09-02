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
            //$table->dropForeign('externalId');
            //$table->dropColumn('externalId');
            //$table->dropForeign('statusId');
            //$table->dropColumn('statusId');
            //$table->dropForeign('syncId');
            //$table->dropColumn('syncId');
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
            //$table->integer('externalId')->unsigned();
            //$table->integer('statusId')->unsigned()->nullable();
            //$table->foreign('statusId')->references('id')->on('services')->onDelete('job_statuses');
            //$table->integer('syncId')->unsigned()->nullable();
            //$table->foreign('syncId')->references('id')->on('services')->onDelete('sync_jobs');
        });
    }
}
