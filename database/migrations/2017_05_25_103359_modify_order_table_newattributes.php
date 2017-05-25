<?php

class ModifyOrderTableNewattributes extends \Illuminate\Database\Migrations\Migration
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

        Schema::table(
            $this->tableName, function ($table) {
                $table->string('orderType')->nullable();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->string('serviceImei')->nullable();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->string('servicePhoneNo')->nullable();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->string('serviceSim')->nullable();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->string('deviceImei')->nullable();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->string('deviceCarrier')->nullable();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->string('deviceSim')->nullable();
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

        Schema::table(
            $this->tableName, function ($table) {
                $table->dropColumn('orderType');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->dropColumn('serviceImei');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->dropColumn('servicePhoneNo');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->dropColumn('serviceSim');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->dropColumn('deviceImei');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->dropColumn('deviceCarrier');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->dropColumn('deviceSim');
        });
    }
}
