<?php

namespace WA\DataStore\DeviceType;

use WA\DataStore\FilterableTransformer;

/**
 * Class AppTransformer.
 */
class DeviceTypeTransformer extends FilterableTransformer
{
    /**
     * @param DeviceType $deviceType
     *
     * @return array
     */
    public function transform(DeviceType $deviceType)
    {
        return [
            'id'          => (int)$deviceType->id,
            'make'        => $deviceType->make,
            'model'       => $deviceType->model,
            'class'       => $deviceType->class,
            'deviceOS'    => $deviceType->deviceOS,
            'description' => $deviceType->description,
            'statusId'    => $deviceType->statusId,
            'image'       => $deviceType->image,
        ];
    }
}
