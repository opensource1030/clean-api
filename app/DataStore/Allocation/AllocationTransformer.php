<?php

namespace WA\DataStore\Allocation;

use WA\DataStore\FilterableTransformer;

class AllocationTransformer extends FilterableTransformer
{
    public function transform(Allocation $allocations)
    {
        return [
            'id'                  => $allocations->id,
            'bill_month'          => $allocations->billMonth,
            'carrier'             => $allocations->carrier,
            'mobile_number'       => $allocations->mobileNumber,
            'currency'            => $allocations->currency,
            'device'              => $allocations->handsetModel,
            'allocated_charge'    => $allocations->totalAllocatedCharge,
            'service_plan_charge' => $allocations->servicePlanCharges,
            'usage_charge'        => $allocations->usageCharges,
            'other_charge'        => $allocations->otherCharges,
            'fees'                => $allocations->fees,
            'last_upgrade'        => $allocations->last_upgrade,
            'voice_category'      => $allocations->voiceCategory,
            'data_category'       => $allocations->dataCategory,
            'messaging'           => $allocations->textMessagingCost,
            'taxes'               => $allocations->taxes,
            'equipment'           => $allocations->equipmentCost,
            'other_category'      => $allocations->otherCategory,
            'unknown_category'    => $allocations->unknownCategory,

        ];
    }
}
