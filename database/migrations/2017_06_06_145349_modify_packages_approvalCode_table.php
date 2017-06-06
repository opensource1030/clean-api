<?php

class ModifyPackagesApprovalCodeTable extends \Illuminate\Database\Migrations\Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'packages';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            $this->tableName, function ($table) {
                $table->string('approvalCode')->nullable();
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
                $table->dropColumn('approvalCode');
        });
    }
}
