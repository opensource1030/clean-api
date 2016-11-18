<?php

namespace WA\DataStore\UdlValue;

use WA\DataStore\FilterableTransformer;

/**
 * Class UdlValueTransformer.
 */
class UdlValueTransformer extends FilterableTransformer
{
    /**
     * @param UdlValue $udlValue
     *
     * @return array
     */
    public function transform(UdlValue $udlValue)
    {
        return [
            'id'            => $udlValue->id,
            'udlName'       => $udlValue->udl->name,
            'udlLabel'      => $udlValue->udl->label,
            'companyName'   => $udlValue->udl->company->name,
            'companyLabel'  => $udlValue->udl->company->label,
            'companySName'  => $udlValue->udl->company->shortName,
        ];
    }
}
