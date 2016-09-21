<?php


namespace WA\DataStore\Allocation;

use WA\DataStore\Allocation\AllocationTransformer;
use WA\DataStore\BaseDataStore;


/**
 * Class Allocations
 *
 * @package WA\DataStore
 * @property-read \WA\DataStore\Company\Company $companies
 * @property-read \WA\DataStore\User\User $users
 * @mixin \Eloquent
 */
class Allocation extends BaseDataStore
{
    protected $table = 'allocations';

    public $timestamps = false;

    protected $fillable = ['userId', 'companyId', 'billMonth', 'mobileNumber', 'carrier', 'currency', 'handsetModel', 'totalAllocatedCharge', 'preAllocatedAmountDue', ' therAdjustments', 'preAdjustedAccessCharge', 'adjustedAccessCost', 'bBCost', 'pDACost', 'iPhoneCost', ' eaturesCost', 'dataCardCost', 'lDCanadaCost', 'uSAddOnPlanCost', 'uSLDAddOnPlanCost', 'uSDataRoamingCost', 'nightAndWeekendAddOnCost', 'minuteAddOnCost', 'servicePlanCharges', 'directConnectCost', 'textMessagingCost', 'dataCost', 'intlRoamingCost', 'intlLongDistanceCost', 'directoryAssistanceCost', 'callForwardingCost', 'airtimeCost', 'usageCharges', 'equipmentCost', 'otherDiscountChargesCost', 'taxes', 'thirdPartyCost', 'otherCharges', 'waFees', 'lineFees', 'mobilityFees', 'fees' ]; //, 'last_upgrade'];


    public function companies()
    {
        return $this->belongsTo('WA\DataStore\Company\Company', 'companyId');
    }

    public function users()
    {
        return $this->belongsTo('WA\DataStore\User\User', 'userId');
    }

    /**
     * Get the transformer instance.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return new AllocationTransformer();
    }


}