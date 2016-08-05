<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyDomains extends Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'company_domains';

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
        Schema::create(
            $this->tableName,
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('domain');
                $table->boolean('active')->default(true);
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
