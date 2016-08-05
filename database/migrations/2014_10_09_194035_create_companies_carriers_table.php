<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use WA\Database\Command\TablesRelationsAndIndexes;


class CreateCompaniesCarriersTable extends Migration {

    use TablesRelationsAndIndexes;


    protected $tableName = 'companies_carriers';

    protected $foreignColumns = [
        'carrierId',
        'companyId',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            $this->tableName,
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('billingAccountNumber');
                $table->string('parentAccountNumber')->nullable();
                $table->string('accountName')->nullable();
                $table->decimal('carrierDiscount', 10, 2)->nullable();
                $table->boolean('active')->default(0);

                $this->includeForeign($table, $this->foreignColumns);


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
        $this->dropForeignKeys($this->tableName, $this->foreignColumns);
        $this->forceDropTable($this->tableName);
    }

}
