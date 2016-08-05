<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use WA\Database\Command\TablesRelationsAndIndexes;

class CreateAttributablesTable extends Migration
{

    use TablesRelationsAndIndexes;

    protected $tableName = 'attributables';
    protected $foreignColumns = [
        'dataOriginationId' => 'nullable'
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'attributables',
            function (Blueprint $table) {

                $table->increments('id');

                $table->string('value')->nullable();
                $table->string('attributable_type');

                $table->string('attribute_id');
                $table->integer('attributable_id');

                $table->index(['attribute_id', 'attributable_id']);

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
        $this->forceDropTable($this->tableName);
    }

}
