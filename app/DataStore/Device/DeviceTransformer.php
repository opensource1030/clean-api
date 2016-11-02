<?php

namespace WA\DataStore\Device;

use WA\DataStore\FilterableTransformer;

/**
 * Class DeviceTransformer.
 */
class DeviceTransformer extends FilterableTransformer
{

    protected $availableIncludes = [
        'assets',
        'carriers',
        'companies',
        'devicetypes',
        'modifications',
        'images',
        'prices'
    ];

    protected $defaultIncludes = [
        'devicetypes'
    ];

    /**
     * @param Device $device
     *
     * @return array
     */
    public function transform(Device $device)
    {
        return [
            'id'             => (int)$device->id,
            'identification' => $device->identification,
            'name'           => $device->name,
            'properties'     => $device->properties,
            'externalId'     => $device->externalId,
            'statusId'       => $device->statusId,
            'syncId'         => $device->syncId,
            'created_at'     => $device->created_at,
            'updated_at'     => $device->updated_at
        ];
    }

}
