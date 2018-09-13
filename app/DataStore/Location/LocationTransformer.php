<?php

namespace WA\DataStore\Location;

use WA\DataStore\FilterableTransformer;

/**
 * Class LocationTransformer.
 */
class LocationTransformer extends FilterableTransformer
{
    /**
     * @param Location $location
     *
     * @return array
     */
    public function transform(Location $location)
    {
        return [

            'id'           => (int)$location->id,
            'name'         => $location->name,
            'fullname'     => $location->fullName,
            'iso2'         => $location->iso2,
            'iso3'         => $location->iso3,
            'region'       => $location->region,
            'currency'     => $location->currency,
            'numCode'      => $location->numCode,
            'callingCode'  => $location->callingCode,
            'lang'         => $location->lang,
            'currencyIso'  => $location->currencyIso,
            'country'      => $location->country,
            'city'         => $location->city,
            'zipCode'      => $location->zipCode,
            'address'      => $location->address,
        ];
    }
}
