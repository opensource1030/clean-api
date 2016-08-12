<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCarriersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'carriers',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('presentation');
                $table->boolean('active')->default(0);
                $table->integer('locationId')->unsigned();
                $table->string('shortName');
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
        Schema::drop('carriers');
    }

}
