<?php


use Illuminate\Database\Migrations\Migration;

class CreateCompanySystemRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_rules', function ( $table) {

            $table->integer('companyId')->unsigned();
            $table->integer('ruleId')->unsigned();
            $table->integer('priority')->unsigned();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('company_rules');
    }
}
