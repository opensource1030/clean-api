<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSyncJobsTable extends Migration
{

    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'sync_jobs';

    protected $foreignColumns = [
        'statusId' => 'job_statuses'
    ];


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_jobs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->integer('statusId');
                $table->text('notes')->nullable();
                $table->timestamps();
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
        Schema::drop('sync_jobs');
    }

}
