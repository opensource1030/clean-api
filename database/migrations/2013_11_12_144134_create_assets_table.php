<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use WA\Database\Command\TablesRelationsAndIndexes;

class CreateAssetsTable extends Migration
{
    use TablesRelationsAndIndexes;

    protected $tableName = 'assets';

    protected $foreignColumns = [
        'typeId'    => 'nullable',
        'carrierId' => 'nullable',
        'statusId'  => 'nullable',
        'syncId'    => 'nullable'
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
                $table->string('identification');
                $table->boolean('active')->default(1);
                $table->integer('externalId')->unique()->nullable();

                $this->includeForeign($table, $this->foreignColumns);

                $table->softDeletes();

                $table->timestamps();
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
