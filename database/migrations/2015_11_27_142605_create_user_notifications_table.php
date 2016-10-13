<?php


use Illuminate\Database\Migrations\Migration;

class CreateUserNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */


    protected $tableName = 'user_notifications';

    public function up()
    {
        Schema::create(
            $this->tableName,
            function ( $table)
            {
                $table->increments('id');
                $table->integer('categoryId')->unsigned();
                $table->integer('userId')->unsigned();
                $table->string('type');
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
