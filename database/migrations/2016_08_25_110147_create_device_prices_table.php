<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicePricesTable extends Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'device_prices';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            $this->tableName,
            function ($table) {
                $table->increments('id');
                $table->integer('priceRetail');
                $table->integer('price1');
                $table->integer('price2');
                $table->integer('priceOwn');
                $table->integer('deviceId')->unsigned();
                $table->integer('modificationId')->unsigned();
                $table->integer('carrierId')->unsigned();
                $table->integer('providerId')->unsigned();
            }
        );

        Schema::table(
            $this->tableName, 
            function($table) {
                $table->foreign('deviceId')->references('id')->on('devices');
                $table->foreign('modificationId')->references('id')->on('modifications');
                $table->foreign('carrierId')->references('id')->on('carriers');
                $table->foreign('providerId')->references('id')->on('providers');
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
                //$table->dropForeign('styleId');
                //$table->dropForeign('capacityId');
                //$table->dropForeign('carrierId');
                //$table->dropForeign('providerId');
        });

        $this->forceDropTable($this->tableName);
    }
}