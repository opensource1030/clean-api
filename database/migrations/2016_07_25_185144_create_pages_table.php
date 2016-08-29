<?php


use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{

    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'pages';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function( $table){

            $table->increments('id');
            $table->string('title');
            $table->string('section');
            $table->text('content');
            $table->boolean('active')->default(0);
            $table->integer('owner_id');
            $table->string('owner_type');
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
        Schema::drop($this->tableName);
    }
}
