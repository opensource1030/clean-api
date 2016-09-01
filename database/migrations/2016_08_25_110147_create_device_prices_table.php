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
                $table->integer('capacityId')->unsigned();
                $table->integer('styleId')->unsigned();
                $table->integer('carrierId')->unsigned();
                $table->integer('companyId')->unsigned();

                $table->nullableTimestamps();
            }
        );

        Schema::table(
            $this->tableName, 
            function($table) {
                $table->foreign('deviceId')->references('id')->on('devices');
                $table->foreign('capacityId')->references('id')->on('modifications');
                $table->foreign('styleId')->references('id')->on('modifications');
                $table->foreign('carrierId')->references('id')->on('carriers');
                $table->foreign('companyId')->references('id')->on('companies');
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