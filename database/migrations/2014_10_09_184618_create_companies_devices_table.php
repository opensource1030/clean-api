<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use WA\Database\Command\TablesRelationsAndIndexes;

class CreateCompaniesDevicesTable extends Migration {

    use TablesRelationsAndIndexes;

    protected $tableName = 'companies_devices';

    protected $foreignColumns = [
        'companyId',
        'deviceId',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');

            $this->includeForeign($table, $this->foreignColumns);
        });
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
