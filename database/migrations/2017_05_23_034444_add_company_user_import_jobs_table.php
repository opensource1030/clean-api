<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyUserImportJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_user_import_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('companyId', false, true);
            $table->string('filepath');
            $table->string('filename');
            $table->integer('totalUsers', false, true);
            $table->integer('createdUsers', false, true)->default(0);
            $table->integer('updatedUsers', false, true)->default(0);
            $table->integer('failedUsers', false, true)->default(0);
            $table->text('fields');
            $table->text('sampleUser');
            $table->text('mappings');
            $table->tinyInteger('status', false, true)->default(0);
            $table->integer('created_by_id', false, true);
            $table->integer('updated_by_id', false, true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_user_import_jobs');
    }
}
