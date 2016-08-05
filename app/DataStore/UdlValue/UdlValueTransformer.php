<?php

namespace WA\DataStore\UdlValue;

use League\Fractal\TransformerAbstract;

/**
 * Class UdlValueTransformer.
 */
class UdlValueTransformer extends TransformerAbstract
{
    /**
     * @param UdlValue $udlValue
     *
     * @return array
     */
    public function transform(UdlValue $udlValue)
    {
        return [
            'id' => $udlValue->id,
            'uld' => $udlValue->udl->name,
            'name' => $udlValue->name,
            'lastUpdated' => $udlValue->updated_at,
        ];
    }
}
