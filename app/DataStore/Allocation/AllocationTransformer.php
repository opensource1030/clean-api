<?php

namespace WA\DataStore\Allocation;

use WA\DataStore\FilterableTransformer;

class AllocationTransformer extends FilterableTransformer
{
    public function transform(Allocation $allocations)
    {
        return [
            'id'                        => (int)$allocations->id,
            'bill_month'                => $allocations->billMonth,
            'carrier'                   => $allocations->carriers->presentation,
            'mobile_number'             => $allocations->mobileNumber,
            'currency'                  => $allocations->currency,
            'device'                    => $allocations->handsetModel,
            'allocated_charge'          => (float)$allocations->totalAllocatedCharge,
            'service_plan_charge'       => (float)$allocations->servicePlanCharges,
            'usage_charge'              => (float)$allocations->usageCharges,
            'other_charge'              => (float)$allocations->otherCharges,
            'fees'                      => (float)$allocations->fees,
            'last_upgrade'              => $allocations->last_upgrade,
            'messaging'                 => (float)$allocations->textMessagingCost,
            'taxes_charge'              => (float)$allocations->taxes,
            'equipment_charge'          => (float)$allocations->equipmentCost,
            'device_type'               => (float)$allocations->deviceType,
            'domestic_usage_charge'     => (float)$allocations->domesticUsageCharge,
            'domestic_data_usage'       => (int) $allocations->domesticDataUsage,
            'domestic_voice_usage'      => (int) $allocations->domesticVoiceUsage,
            'domestic_text_usage'       => (int) $allocations->domesticTextUsage,
            'intl_roam_usage_charge'    => (float) $allocations->intlRoamUsageCharge,
            'int_roam_data_usage'       => (int) $allocations->intlRoamDataUsage,
            'intl_roam_voice_usage'     => (int) $allocations->intlRoamVoiceUsage,
            'intl_roam_text_usage'      => (int) $allocations->intlRoamTextUsage,
            'intl_ld_usage_charge'      => (float) $allocations->intlLDUsageCharge,
            'intl_ld_voice_usage'       => (int) $allocations->intlLDVoiceUsage,
            'intl_ld_text_usage'        => (int) $allocations->intlLDTextUsage,
            'etf_charge'                => (float) $allocations->etfCharge,
            'other_carrier_charge'      => (float) $allocations->otherCarrierCharge,
        ];
    }
}
