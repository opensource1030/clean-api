<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use WA\Database\Command\TablesRelationsAndIndexes;

class CreateAssetDeviceTable extends Migration
{
    use TablesRelationsAndIndexes;

    protected $tableName = 'asset_devices';

    protected $foreignColumns = [
        'assetId',
        'deviceId',
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
                $table->timestamps();

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
