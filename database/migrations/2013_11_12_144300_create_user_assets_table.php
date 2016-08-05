<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use WA\Database\Command\TablesRelationsAndIndexes;


class CreateUserAssetsTable extends Migration
{
    use TablesRelationsAndIndexes;

    protected $tableName = 'user_assets';

    protected $foreignColumns = [
        'employeeId',
        'assetId',
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

                $this->includeForeign($table, $this->foreignColumns);
                $table->nullableTimestamps();
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
