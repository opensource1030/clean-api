<?php

class ModifyCompanyUserImportJobsTable extends \Illuminate\Database\Migrations\Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'company_user_import_jobs';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            $this->tableName, function ($table) {
                $table->integer('creatableUsers')->nullable();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->integer('updatableUsers')->nullable();
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->string('jobType')->nullable();
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
                $table->dropColumn('creatableUsers');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->dropColumn('updatableUsers');
        });

        Schema::table(
            $this->tableName, function ($table) {
                $table->dropColumn('jobType');
        });
    }
}
