<?php

use Illuminate\Database\Migrations\Migration;


class CreateCompaniesShortName extends Migration
{

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('companies', function ( $table) {
      $table->string('shortName')->before('rawDataDirectoryPath');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('companies', function ( $table) {
      $table->dropColumn('shortName');
    });
  }

}
