<?php

namespace WA\DataStore\Allocation;

use League\Fractal\TransformerAbstract;

class AllocationTransformer extends TransformerAbstract
{
    public function transform(Allocation $allocations)
    {
        return [
            'id' => $allocations->id,
            'bill_month' => $allocations->billMonth,
            'carrier' => $allocations->carrier,
            'mobile_number' => $allocations->mobileNumber,
            'currency' => $allocations->currency,
            'device' => $allocations->handsetModel,
            'allocated_charge' => $allocations->totalAllocatedCharge,
            'service_plan_charge' => $allocations->servicePlanCharges,
            'usage_charge' => $allocations->usageCharges,
            'other_charge' => $allocations->otherCharges,
            'fees' => $allocations->fees,
        ];

    }

}