<?php

class ModifyOrderTableNullableattributes extends \Illuminate\Database\Migrations\Migration
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
                $table->dropForeign('orders_packageid_foreign');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->dropForeign('orders_serviceid_foreign');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->integer('packageId')->unsigned()->nullable()->change();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->integer('serviceId')->unsigned()->nullable()->change();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->foreign('packageId')->references('id')->on('packages');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->foreign('serviceId')->references('id')->on('services');
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
                $table->dropForeign('orders_packageid_foreign');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->dropForeign('orders_serviceid_foreign');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->integer('packageId')->unsigned()->change();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->integer('serviceId')->unsigned()->change();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->foreign('packageId')->references('id')->on('packages')->onDelete('cascade');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->foreign('serviceId')->references('id')->on('services')->onDelete('cascade');
        });
    }
}
