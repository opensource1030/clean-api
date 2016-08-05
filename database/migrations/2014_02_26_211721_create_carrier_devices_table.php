<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use WA\Database\Command\TablesRelationsAndIndexes;

class CreateCarrierDevicesTable extends Migration
{
    use TablesRelationsAndIndexes;

    protected $tableName = 'carrier_devices';

    protected $foreignColumns = [
        'statusId'  => 'nullable',
        'carrierId'    => 'nullable',
        'deviceTypeId' => 'nullable'
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
                $table->string('make')->nullable();
                $table->string('model')->nullable();
                $table->string('makeModel')->nullable();
                $table->string('WA_alias')->nullable();
                $table->string('class')->nullable();
                $table->string('deviceOS')->nullable();
                $table->string('description')->nullable();
                $table->boolean('isLive')->default(0);
                $table->integer('externalId')->default(0);

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
