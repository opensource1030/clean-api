<?php

namespace WA\DataStore\Allocation;

use WA\DataStore\FilterableTransformer;

class AllocationTransformer extends FilterableTransformer
{
    public function transform(Allocation $allocations)
    {
        return [
            'id'                  => (int)$allocations->id,
            'bill_month'          => $allocations->billMonth,
            'carrier'             => $allocations->carrier,
            'mobile_number'       => $allocations->mobileNumber,
            'currency'            => $allocations->currency,
            'device'              => $allocations->handsetModel,
            'allocated_charge'    => (float)$allocations->totalAllocatedCharge,
            'service_plan_charge' => (float)$allocations->servicePlanCharges,
            'usage_charge'        => (float)$allocations->usageCharges,
            'other_charge'        => (float)$allocations->otherCharges,
            'fees'                => (float)$allocations->fees,
            'last_upgrade'        => $allocations->last_upgrade,
            'voice_category'      => (float)$allocations->voiceCategory,
            'data_category'       => (float)$allocations->dataCategory,
            'messaging'           => (float)$allocations->textMessagingCost,
            'taxes'               => (float)$allocations->taxes,
            'equipment'           => (float)$allocations->equipmentCost,
            'other_category'      => (float)$allocations->otherCategory,
            'unknown_category'    => (float)$allocations->unknownCategory,
        ];
    }
}
