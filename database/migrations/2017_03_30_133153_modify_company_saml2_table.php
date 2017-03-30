<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyCompanySaml2Table extends Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'company_saml2';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            $this->tableName,
            function ($table) {
                $table->string('emailAttribute');
                $table->string('firstNameAttribute');
                $table->string('lastNameAttribute');
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
            $this->tableName, function ($table) {
                $table->dropColumn('emailAttribute');
                $table->dropColumn('firstNameAttribute');
                $table->dropColumn('lastNameAttribute');
        });
    }
}
