<?php


use Illuminate\Database\Migrations\Migration;

class CreateOrders extends Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'orders';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            $this->tableName,
            function ($table) {
                $table->increments('id');
                $table->string('status');
                $table->integer('userId')->unsigned();
                $table->integer('packageId')->unsigned();

                $table->nullableTimestamps();
            }
        );

        Schema::table(
            $this->tableName, 
            function($table) {
                $table->foreign('userId')->references('id')->on('users');
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
        Schema::table(
            $this->tableName, 
            function ( $table) {
                //$table->dropForeign('userId');
                //$table->dropForeign('packageId');
        });

        $this->forceDropTable($this->tableName);
    }
}

/* EXAMPLE

{
  "id": 1,
  "status": "new",
  "created_at": "",
  "idEmployee": "WA-C4KX6JKVHA",
  "idPackage": 2,
  "links": {
    "self": {
      "href": "/order/1"
    }
  }
}
*/