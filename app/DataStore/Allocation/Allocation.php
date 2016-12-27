<?php

namespace WA\DataStore\Allocation;

use WA\DataStore\BaseDataStore;

/**
 * Class Allocations.
 *
 * @property-read \WA\DataStore\Company\Company $companies
 * @property-read \WA\DataStore\User\User $users
 * @property-read \WA\DataStore\User\User $carriers
 * @mixin \Eloquent
 */
class Allocation extends BaseDataStore
{
    protected $table = 'allocations';

    public $timestamps = false;

    protected $fillable = ['userId', 'companyId', 'billMonth', 'mobileNumber', 'carrier', 'currency', 'handsetModel', 'totalAllocatedCharge', 'preAllocatedAmountDue', 'therAdjustments', 'preAdjustedAccessCharge', 'adjustedAccessCost', 'bBCost', 'pDACost', 'iPhoneCost', 'featuresCost', 'dataCardCost', 'lDCanadaCost', 'uSAddOnPlanCost', 'uSLDAddOnPlanCost', 'uSDataRoamingCost', 'nightAndWeekendAddOnCost', 'minuteAddOnCost', 'servicePlanCharges', 'directConnectCost', 'textMessagingCost', 'dataCost', 'intlRoamingCost', 'intlLongDistanceCost', 'directoryAssistanceCost', 'callForwardingCost', 'airtimeCost', 'usageCharges', 'equipmentCost', 'otherDiscountChargesCost', 'taxes', 'thirdPartyCost', 'otherCharges', 'waFees', 'lineFees', 'mobilityFees', 'fees', 'last_upgrade', 'otherAdjustments', 'featuresCost'];

    public function companies()
    {
        return $this->belongsTo('WA\DataStore\Company\Company', 'companyId');
    }

    public function users()
    {
        return $this->belongsTo('WA\DataStore\User\User', 'userId');
    }

    public function carriers()
    {
        return $this->belongsTo('WA\DataStore\Carrier\Carrier', 'carrier');
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
