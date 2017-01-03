<?php

namespace WA\DataStore\Order;

use WA\DataStore\FilterableTransformer;
use League\Fractal\Resource\Collection as ResourceCollection;
use WA\DataStore\ServiceItem\ServiceItemTransformer;
use WA\DataStore\App\AppTransformer;
use WA\DataStore\User\UserTransformer;
use WA\DataStore\Package\PackageTransformer;
use WA\DataStore\Service\ServiceTransformer;
use WA\DataStore\DeviceVariation\DeviceVariationTransformer;
use WA\DataStore\Carrier\CarrierTransformer;

/**
 * Class OrderTransformer.
 */
class OrderTransformer extends FilterableTransformer
{
    protected $availableIncludes = [
        'users',
        'packages',
        'services',
        'apps',
        'devicevariations'
    ];

    /**
     * @param Order $order
     *
     * @return array
     */
    public function transform(Order $order)
    {
        return [
            'id'         => (int)$order->id,
            'status'     => $order->status,
            'userId'     => (int)$order->userId,
            'packageId'  => (int)$order->packageId,
            'serviceId'  => (int)$order->serviceId,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
        ];
    }


    public function includeServiceitems(Order $order)
    {
        $this->criteria = $this->getRequestCriteria();
        $serviceItems = $this->applyCriteria($order->serviceitems(), $this->criteria, true, [
            'serviceitems' => 'service_items'
        ]);
        return new ResourceCollection ($serviceItems->get(), new ServiceItemTransformer(), 'service_items');
    }


    public function includeApps(Order $order)
    {
        $this->criteria = $this->getRequestCriteria();
        $apps = $this->applyCriteria($order->apps(), $this->criteria, true, [
            'apps' => 'apps'
        ]);
        return new ResourceCollection ($apps->get(), new AppTransformer(), 'apps');
    }

    public function includeUsers(Order $order)
    {
        $this->criteria = $this->getRequestCriteria();
        $users = $this->applyCriteria($order->users(), $this->criteria, true, [
            'users' => 'users'
        ]);
        return new ResourceCollection ($users->get(), new UserTransformer(), 'users');
    }

    public function includePackages(Order $order)
    {
        $this->criteria = $this->getRequestCriteria();
        $packages = $this->applyCriteria($order->packages(), $this->criteria, true, [
            'packages' => 'packages'
        ]);
        return new ResourceCollection ($packages->get(), new PackageTransformer(), 'packages');
    }

    public function includeDeviceVariations(Order $order)
    {
        $this->criteria = $this->getRequestCriteria();
        $deviceVariations = $this->applyCriteria($order->deviceVariations(), $this->criteria, true, [
            'devicevariations' => 'devicevariations'
        ]);
        return new ResourceCollection ($deviceVariations->get(), new DeviceVariationTransformer(), 'devicevariations');
    }

    public function includeServices(Order $order)
    {
        $this->criteria = $this->getRequestCriteria();
        $services = $this->applyCriteria($order->services(), $this->criteria, true, [
            'services' => 'services'
        ]);
        return new ResourceCollection ($services->get(), new ServiceTransformer(), 'services');
    }

}
