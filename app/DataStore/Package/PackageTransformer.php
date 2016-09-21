<?php

namespace WA\DataStore\Package;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item as ResourceItem;
use League\Fractal\TransformerAbstract;

/**
 * Class PackageTransformer
 *
 */
class PackageTransformer extends TransformerAbstract
{

    /**
     * @param Package $Package
     *
     * @return array
     */
    public function transform(Package $package)
    {
        return [
            'id' => (int)$package->id,
            'name' => $package->name,
            'created_at' => $package->created_at,
            'updated_at' => $package->updated_at,
        ];
    }

    /**
     * @param Package $package
     *
     * @return ResourceCollection
     */
    public function includeConditions(Package $package)
    {
        return new ResourceCollection($package->conditions, new ConditionTransformer(),'conditions');
    }

    /**
     * @param Package $package
     *
     * @return ResourceCollection
     */
    public function includeServices(Package $package)
    {
        return new ResourceCollection($package->services, new ServiceTransformer(),'services');
    }

    /**
     * @param Package $package
     *
     * @return ResourceCollection
     */
    public function includeDevices(Package $package)
    {
        return new ResourceCollection($package->devices, new DeviceTransformer(),'devices');
    }

    /**
     * @param Package $package
     *
     * @return ResourceCollection
     */
    public function includeApps(Package $package)
    {
        return new ResourceCollection($package->apps, new AppTransformer(),'apps');
    }
}