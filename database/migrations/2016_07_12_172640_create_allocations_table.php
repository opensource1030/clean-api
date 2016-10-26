<?php


use Illuminate\Database\Migrations\Migration;

class CreateAllocationsTable extends Migration
{
    use \WA\Database\Command\TablesRelationsAndIndexes;

    protected $tableName = 'allocations';

    protected $foreignColumns = [
        'userId',
        'companyId',
    ];

    /**
     * Run the migrations.asset_devices.
     */
    public function up()
    {
        Schema::create(
            $this->tableName,
            function ($table) {
                $table->increments('id');
                $table->integer('userId')->unsigned();
                $table->integer('companyId')->unsigned();

                $table->date('billMonth');
                $table->string('mobileNumber');
                $table->string('carrier');
                $table->string('currency');
                $table->string('handsetModel');

                $table->decimal('totalAllocatedCharge');
                $table->decimal('preAllocatedAmountDue');
                $table->decimal('otherAdjustments');
                $table->decimal('preAdjustedAccessCharge');

                // Service Plan Charges
                $table->decimal('adjustedAccessCost');
                $table->decimal('bBCost');
                $table->decimal('pDACost');
                $table->decimal('iPhoneCost');
                $table->decimal('featuresCost');
                $table->decimal('dataCardCost');
                $table->decimal('lDCanadaCost');
                $table->decimal('uSAddOnPlanCost');
                $table->decimal('uSLDAddOnPlanCost');
                $table->decimal('uSDataRoamingCost');
                $table->decimal('nightAndWeekendAddOnCost');
                $table->decimal('minuteAddOnCost');

                $table->decimal('servicePlanCharges');

                //Usage Charges
                $table->decimal('directConnectCost');
                $table->decimal('textMessagingCost');
                $table->decimal('dataCost');
                $table->decimal('intlRoamingCost');
                $table->decimal('intlLongDistanceCost');
                $table->decimal('directoryAssistanceCost');
                $table->decimal('callForwardingCost');
                $table->decimal('airtimeCost');

                // summation of usage charges
                $table->decimal('usageCharges');

                //Other Charges
                $table->decimal('equipmentCost');
                $table->decimal('otherDiscountChargesCost');
                $table->decimal('taxes');
                $table->decimal('thirdPartyCost');

                //summation of other charges
                $table->decimal('otherCharges');

                //Fees
                $table->decimal('waFees');
                $table->decimal('lineFees');
                $table->decimal('mobilityFees');

                //summation of fees
                $table->decimal('fees');

                //last upgrade date
                $table->string('last_upgrade');
            });

        Schema::table(
            $this->tableName,
            function ($table) {
                $table->foreign('userId')->references('id')->on('users');
                $table->foreign('companyId')->references('id')->on('companies');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table(
            $this->tableName,
            function ($table) {
                //$table->dropForeign('userId');
                //$table->dropForeign('companyId');
            });

        $this->forceDropTable($this->tableName);
    }
}
