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
        'devicevariations',
        'addresses'
    ];

    /**
     * @param Order $order
     *
     * @return array
     */
    public function transform(Order $order)
    {
        return [
            'id'                => (int)$order->id,
            'status'            => $order->status,
            'orderType'         => $order->orderType,
            'serviceImei'       => $order->serviceImei,
            'servicePhoneNo'    => $order->servicePhoneNo,
            'serviceSim'        => $order->serviceSim,
            'deviceImei'        => $order->deviceImei,
            'deviceCarrier'     => $order->deviceCarrier,
            'deviceSim'         => $order->deviceSim,
            'userId'            => (int)$order->userId,
            'packageId'         => isset($order->packageId) ? (int)$order->packageId : $order->packageId,
            'serviceId'         => isset($order->serviceId) ? (int)$order->serviceId : $order->serviceId,
            'addressId'         => isset($order->addressId) ? (int)$order->addressId : $order->addressId,
            'created_at'        => $order->created_at,
            'updated_at'        => $order->updated_at,
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
}
