<?php

namespace WA\DataStore\Package;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item as ResourceItem;
use League\Fractal\TransformerAbstract;

use WA\DataStore\App\AppTransformer;
use WA\DataStore\Condition\ConditionTransformer;
use WA\DataStore\Service\ServiceTransformer;
use WA\DataStore\Device\DeviceTransformer;

use WA\Helpers\Traits\Criteria;

/**
 * Class PackageTransformer
 *
 */
class PackageTransformer extends TransformerAbstract
{
    use Criteria;

    protected $availableIncludes = [
        'conditions', 'services', 'devices', 'apps'
    ];

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
            'addressId' => $package->addressId,
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
        $conditions = $this->applyCriteria($package->conditions(), $this->criteria);
        return new ResourceCollection($conditions->get(), new ConditionTransformer(), 'conditions');
    }

    /**
     * @param Package $package
     *
     * @return ResourceCollection
     */
    public function includeServices(Package $package)
    {
        $services = $this->applyCriteria($package->services(), $this->criteria);
        return new ResourceCollection($services->get(), new ServiceTransformer(), 'services');
    }

    /**
     * @param Package $package
     *
     * @return ResourceCollection
     */
    public function includeDevices(Package $package)
    {
        $devices = $this->applyCriteria($package->devices(), $this->criteria);
        return new ResourceCollection($devices->get(), new DeviceTransformer(), 'devices');
    }

    /**
     * @param Package $package
     *
     * @return ResourceCollection
     */
    public function includeApps(Package $package)
    {
        $apps = $this->applyCriteria($package->apps(), $this->criteria);
        return new ResourceCollection($apps->get(), new AppTransformer(), 'apps');
    }
}