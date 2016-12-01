<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use WA\Database\Command\TablesRelationsAndIndexes;

class ModifyOrdersTable extends Migration
{
    use TablesRelationsAndIndexes;

    protected $tableName = 'orders';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            $this->tableName, function ($table) {
                $table->integer('carrierId')->unsigned()->nullable();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->foreign('carrierId')->references('id')->on('carriers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            $this->tableName, function ($table) {
                $table->dropForeign('orders_carrierid_foreign');
                $table->dropColumn('carrierId');
        });
    }
}
