<?php

namespace WA\DataStore\Carrier;

use League\Fractal\TransformerAbstract;

use League\Fractal\Resource\Collection as ResourceCollection;

use WA\DataStore\Image\ImageTransformer;

/**
 * Class CarrierTransformer.
 */
class CarrierTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'images'
    ];
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

    /**
     * @param Carrier $carrier
     *
     * @return ResourceCollection
     */
    public function includeImages(Carrier $carrier)
    {
        return new ResourceCollection($carrier->images, new ImageTransformer(),'images');
    }
}
