<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExternalIdentitication2ToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * This currently stores the easyvista "DEFAULT_DOMAIN_ID"
         */
        Schema::table('companies', function (Blueprint $table) {
            $table->integer('externalId')->nullable()->after('defaultLocation');
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->integer('externalId2')->nullable()->after('externalId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('externalId');
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('externalId2');
        });
    }
}
