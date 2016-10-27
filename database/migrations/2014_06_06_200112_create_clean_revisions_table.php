<?php

use Illuminate\Database\Migrations\Migration;

class CreateCleanRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(
            'revisions',
            function ($table) {
                $table->increments('id');
                $table->string('revisionable_type');
                $table->integer('revisionable_id');
                $table->integer('user_id')->nullable();
                $table->string('key');
                $table->text('old_value')->nullable();
                $table->text('new_value')->nullable();
                $table->nullableTimestamps();

                $table->index(array('revisionable_id', 'revisionable_type'));
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('revisions');
    }
}
