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
            'carrier'             => $allocations->carriers->presentation,
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
            'device_type'         => (float)$allocations->deviceType,
            'domestic_usage'      => (float)$allocations->domesticUsageCharge,
            'domestic_data'       => (float) $allocations->domesticDataUsage,
            'domestic_voice'      => (float) $allocations->domesticVoiceUsage,
            'domestic_text'       => (float) $allocations->domesticText,
            'intl_roam_usage'     => (float) $allocations->intlUsage,
            'int_roam_data'       => (float) $allocations->intlRoamData,
            'intl_roam_voice'     => (float) $allocations->intlRoamVoice,
            'intl_roam_text'      => (float) $allocations->intlRoamText,
            'intl_ld_usage'       => (float) $allocations->intlLDUsage,
            'intl_ld_voice'       => (float) $allocations->intlLDVoice,
            'intl_ld_text'        => (float) $allocations->intlLDText,
            'etf'                 => (float) $allocations->etf,
            'other_carrier_charges' => (float) $allocations->otherCarrierCharges,
        ];
    }
}
