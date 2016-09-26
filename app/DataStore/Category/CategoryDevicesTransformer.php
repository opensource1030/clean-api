<?php

namespace WA\DataStore\Category;

use League\Fractal\TransformerAbstract;

use League\Fractal\Resource\Collection as ResourceCollection;

use WA\DataStore\Device\DeviceTransformer;

/**
 * Class CarrierTransformer.
 */
class CategoryDevicesTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'devices'
    ];
    /**
     * @param CategoryDevices $categoryDevices
     *
     * @return array
     */
    public function transform(CategoryDevices $categoryDevices)
    {
        return [
            'id' => $categoryDevices->id,
            'name' => $categoryDevices->name
        ];
    }

    /**
     * @param CategoryDevices $categoryDevices
     *
     * @return ResourceCollection
     */
    public function includeDevices(CategoryDevices $categoryDevices)
    {
        return new ResourceCollection($categoryDevices->images, new DeviceTransformer(),'devices');
    }

    /**
     * @param CategoryDevices $categoryDevices
     *
     * @return ResourceCollection
     */
    public function includeImages(CategoryDevices $categoryDevices)
    {
        return new ResourceCollection($categoryDevices->images, new ImageTransformer(),'images');
    }
}