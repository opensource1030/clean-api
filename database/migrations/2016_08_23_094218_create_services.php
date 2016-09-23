<?php


use Illuminate\Database\Migrations\Migration;

class CreateServices extends Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'services';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            $this->tableName,
            function ( $table) {
                $table->increments('id');
                $table->string('title');
                $table->integer('planCode');
                $table->integer('cost');
                $table->string('description');
                $table->integer('domesticMinutes');
                $table->integer('domesticData');
                $table->integer('domesticMessages');
                $table->integer('internationalMinutes');
                $table->integer('internationalData');
                $table->integer('internationalMessages');

                $table->nullableTimestamps();

                //$table->integer('companyId')->unsigned();
            }
        );

        Schema::table(
            $this->tableName, 
            function($table) {
                //$table->foreign('companyId')->references('id')->on('companies');
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
                //$table->dropForeign('companyId');
        });

        $this->forceDropTable($this->tableName);
    }
}

/* EXAMPLE

{
    "id": 1,
    "title": "Pooled Domestic Voice Plan",
    "planCode": 55555,
    "cost": 25,
    "description": "Reduces the per minute rate for calls originating from outside the U.S.",
    "domesticMinutes": 3000,
    "domesticData": 3000,
    "domesticMessages": 3000,
    "internationalMinutes": 3000,
    "internationalData": 3000,
    "internationalMessages": 3000,
    "links": {
        "self": {
            "href": "service/1"
        }
    }
}
*/