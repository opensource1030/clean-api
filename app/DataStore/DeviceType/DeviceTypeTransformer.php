<?php

namespace WA\DataStore\DeviceType;

use League\Fractal\TransformerAbstract;

/**
 * Class AppTransformer.
 */
class DeviceTypeTransformer extends TransformerAbstract
{
    /**
     * @param DeviceType $deviceType
     *
     * @return array
     */
    public function transform(DeviceType $deviceType)
    {
        return [
            'id' => (int) $deviceType->id,
            'make' => $deviceType->make,
            'model' => $deviceType->model,
            'class' => $deviceType->class,
            'deviceOS' => $deviceType->deviceOS,
            'description' => $deviceType->description,
            'statusId' => $deviceType->statusId,
            'image' => $deviceType->image,
        ];
    }
}
