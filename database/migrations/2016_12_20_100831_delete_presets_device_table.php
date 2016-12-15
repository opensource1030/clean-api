<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeletePresetsDeviceTable extends Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;
     protected $tableName = 'preset_devices';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        /*Schema::table(
            $this->tableName, function ($table) {
                $table->integer('presetId')->unsigned()->nullable();
                
        });
        Schema::table(
            $this->tableName, function ($table) {
                $table->integer('deviceId')->unsigned()->nullable();
                
        });
        Schema::table(
            $this->tableName, function ($table) {
                $table->foreign('presetId')->references('id')->on('presets');
            }
        );
        Schema::table(
            $this->tableName, function ($table) {
                $table->foreign('deviceId')->references('id')->on('devices');
            }
        );*/
        $this->forceDropTable($this->tableName);
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
            function ($table) {
                $table->foreign('presetId')->references('id')->on('presets')->onDelete('cascade');
            }
        );
           Schema::table(
            $this->tableName,
            function ($table) {
                $table->foreign('deviceId')->references('id')->on('devices')->onDelete('cascade');
            }
        );
    }
}
