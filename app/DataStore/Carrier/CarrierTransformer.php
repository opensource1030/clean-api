<?php

namespace WA\DataStore\Carrier;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as ResourceCollection;
use WA\DataStore\Image\ImageTransformer;
use WA\Helpers\Traits\Criteria;

/**
 * Class CarrierTransformer.
 */
class CarrierTransformer extends TransformerAbstract
{
    use Criteria;

    protected $availableIncludes = [
        'images',
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
            'shortName' => $carrier->shortName,
            'created_at' => $carrier->created_at,
            'updated_at' => $carrier->updated_at,
        ];
    }

    /**
     * @param Carrier $carrier
     *
     * @return ResourceCollection
     */
    public function includeImages(Carrier $carrier)
    {
        $images = $this->applyCriteria($carrier->images(), $this->criteria);

        return new ResourceCollection($images->get(), new ImageTransformer(), 'images');
    }
}
