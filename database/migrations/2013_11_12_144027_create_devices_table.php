<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use WA\Database\Command\TablesRelationsAndIndexes;

class CreateDevicesTable extends Migration
{

    use TablesRelationsAndIndexes;

    protected $tableName = 'devices';

    protected $foreignColumns = [
        'deviceTypeId',
        'statusId' => 'nullable',
        'carrierId' => 'nullable',
        'syncId'   => 'nullable'
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
                $table->string('identification')->unique();
                $table->integer('externalId')->unique()->nullable();
                $this->includeForeign($table, $this->foreignColumns);

                $table->nullableTimestamps();
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
