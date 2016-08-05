<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use WA\Database\Command\TablesRelationsAndIndexes;

class CreateDeviceTypesTable extends Migration {

    use TablesRelationsAndIndexes;

    protected $tableName = 'device_types';

    protected $foreignColumns = [
        'statusId' => "nullable",
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'device_types',
            function (Blueprint $table) {

                $table->increments('id');

                $table->string('make');
                $table->string('model');
                $table->string('class');
                $table->string('deviceOS')->nullable();
                $table->string('description')->nullable();
                $this->includeForeign($table, $this->foreignColumns);
                $table->string('image')->nullable();
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
