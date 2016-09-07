<?php

namespace WA\DataStore\Asset;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\TransformerAbstract;
use WA\DataStore\Device\DeviceTransformer;
use WA\DataStore\User\UserTransformer;

/**
 * Class AssetTransformer.
 */
class AssetTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'users',
        'devices',
        'carriers',
        'companies',
    ];

    /**
     * @param Asset $asset
     *
     * @return array
     */
    public function transform(Asset $asset)
    {
        return [
            'id' => (int)$asset->id,
            'identification' => $asset->identification,
            'active' => $asset->active,
            'statusId' => $asset->statusId,
            'typeId' => $asset->typeId,
            'externalId' => $asset->externalId,
            'carrierId' => $asset->carrierId,
            'syncId' => $asset->syncId
        ];
    }

    /**
     * @param Asset $asset
     *
     * @return ResourceCollection
     */
    public function includeUsers(Asset $asset)
    {
        return new ResourceCollection($asset->users, new UserTransformer(), 'users');
    }

    /**
     * @param Asset $asset
     *
     * @return ResourceCollection
     */
    public function includeDevices(Asset $asset)
    {
        return new ResourceCollection($asset->devices, new DeviceTransformer(), 'devices');
    }

}
