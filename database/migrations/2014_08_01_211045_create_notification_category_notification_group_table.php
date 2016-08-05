<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use WA\Database\Command\TablesRelationsAndIndexes;

class CreateNotificationCategoryNotificationGroupTable extends Migration
{

    use TablesRelationsAndIndexes;

    protected $tableName = "notifications_categories_in_groups";

    protected $foreignColumns = [
        'category_id',
        'group_id',
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
