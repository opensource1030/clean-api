<?php

namespace WA\DataStore\Address;

use League\Fractal\TransformerAbstract;

/**
 * Class AddressTransformer.
 */
class AddressTransformer extends TransformerAbstract
{
    /**
     * @param Address $address
     *
     * @return array
     */
    public function transform(Address $address)
    {
        return [

            'id' => (int) $address->id,
            'address' => $address->address,
            'city' => $address->city,
            'state' => $address->state,
            'country' => $address->country,
            'postalCode' => $address->postalCode,
            'created_at' => $address->created_at,
            'updated_at' => $address->updated_at,
        ];
    }
}
