<?php

namespace WA\DataStore\Category;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as ResourceCollection;

use WA\DataStore\Device\DeviceTransformer;
use WA\DataStore\Image\ImageTransformer;

/**
 * Class CarrierTransformer.
 */
class PresetTransformer extends TransformerAbstract
{
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
        return new ResourceCollection($preset->devices, new DeviceTransformer(),'devices');
    }

    /**
     * @param Preset $preset
     *
     * @return ResourceCollection
     */
    public function includeImages(Preset $preset)
    {
        return new ResourceCollection($preset->images, new ImageTransformer(),'images');
    }
}