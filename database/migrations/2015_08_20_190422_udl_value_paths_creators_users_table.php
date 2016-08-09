<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UdlValuePathsCreatorsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('udl_value_paths_creators_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('udlValuePathId');
            $table->integer('creatorId');
            $table->string('userEmail');
            $table->string('userFirstName');
            $table->string('userLastName');
            $table->string('userUserId');
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
        Schema::drop('udl_value_paths_creators_users');
    }
}
