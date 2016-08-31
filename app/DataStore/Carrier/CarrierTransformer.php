<?php

namespace WA\DataStore\Carrier;

use League\Fractal\TransformerAbstract;

/**
 * Class CarrierTransformer.
 */
class CarrierTransformer extends TransformerAbstract
{
    /**
     * @param Carrier $carrier
     *
     * @return array
     */
    public function transform(Carrier $carrier)
    {
        return [
            'id' => $carrier->id,
            'name' => $carrier->name,
            'presentation' => $carrier->presentation,
            'active' => $carrier->active,
            'locationId' => $carrier->locationId,
            'shortName' => $carrier->shortName
        ];
    }
}
