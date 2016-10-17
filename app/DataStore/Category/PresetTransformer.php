<?php

namespace WA\DataStore\Category;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as ResourceCollection;

use WA\DataStore\Device\DeviceTransformer;
use WA\DataStore\Image\ImageTransformer;

use WA\Helpers\Traits\Criteria;

/**
 * Class CarrierTransformer.
 */
class PresetTransformer extends TransformerAbstract
{
    use Criteria;

    protected $availableIncludes = [
        'devices', 'images'
    ];
    /**
     * @param Preset $preset
     *
     * @return array
     */
    public function transform(Preset $preset)
    {
        return [
            'id' => $preset->id,
            'name' => $preset->name,
            'created_at' => $preset->created_at,
            'updated_at' => $preset->updated_at
        ];
    }

    /**
     * @param Preset $preset
     *
     * @return ResourceCollection
     */
    public function includeDevices(Preset $preset)
    {
        $devices = $this->applyCriteria($preset->devices(), $this->criteria);
        return new ResourceCollection($devices->get(), new DeviceTransformer(), 'devices');
    }

    /**
     * @param Preset $preset
     *
     * @return ResourceCollection
     */
    public function includeImages(Preset $preset)
    {
        $images = $this->applyCriteria($preset->images(), $this->criteria);
        return new ResourceCollection($images->get(), new ImageTransformer(), 'images');
    }
}