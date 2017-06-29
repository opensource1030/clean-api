<?php

class CreateOrderJobsTable extends \Illuminate\Database\Migrations\Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'order_jobs';

    
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
                $table->integer('orderId')->unsigned();
                $table->string('statusBefore');
                $table->string('statusAfter');
            }
        );

        Schema::table(
            $this->tableName,
            function ($table) {
                $table->foreign('orderId')->references('id')->on('orders')->onDelete('cascade');
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
        $this->forceDropTable($this->tableName);
    }
}
