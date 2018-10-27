<?php

namespace WA\DataStore\UdlValue;

use WA\DataStore\FilterableTransformer;

/**
 * Class UdlValueTransformer.
 */
class UdlValueTransformer extends FilterableTransformer
{
    protected $availableIncludes = [
        'udls'
    ];

    /**
     * @param UdlValue $udlValue
     *
     * @return array
     */
    public function transform(UdlValue $udlValue)
    {
        return [    
            'id'            => $udlValue->id,
            'udlId'         => $udlValue->udlId,
            'udlName'       => $udlValue->udls->name,
            'udlValue'      => $udlValue->name
        ];
    }
}
