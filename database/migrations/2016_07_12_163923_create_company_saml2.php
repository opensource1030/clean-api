<?php

/**
 * company_saml2 - Table of the Companies with saml2_settings configuration.
 *  
 * @author   Agustí Dosaiguas
 */


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanySaml2 extends Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'company_saml2';
    
    protected $foreignColumns = [
        'companyId'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // config/saml2_settings.
        Schema::create(
            $this->tableName,
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('entityId');
                $table->string('singleSignOnServiceUrl');
                $table->string('singleSignOnServiceBinding');
                $table->string('singleLogoutServiceUrl');
                $table->string('singleLogoutServiceBinding');
                $table->longtext('x509cert');
                $this->includeForeign($table, $this->foreignColumns);
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
        $this->dropForeignKeys($this->tableName, $this->foreignColumns);
        $this->forceDropTable($this->tableName);
    }
}