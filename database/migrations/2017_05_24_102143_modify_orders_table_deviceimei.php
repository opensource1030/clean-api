<?php

//use Illuminate\Support\Facades\Schema;
//use Illuminate\Database\Schema\Blueprint;
//use Illuminate\Database\Migrations\Migration;

class ModifyOrdersTableDeviceimei extends \Illuminate\Database\Migrations\Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;

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
                $table->string('phoneno')->nullable();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->string('imei')->nullable();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->string('sim')->nullable();
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
                $table->dropColumn('phoneno');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->dropColumn('imei');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->dropColumn('sim');
        });
    }
}
