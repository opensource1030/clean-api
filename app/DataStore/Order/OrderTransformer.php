<?php

namespace WA\DataStore\Order;

use WA\DataStore\FilterableTransformer;

/**
 * Class OrderTransformer.
 */
class OrderTransformer extends FilterableTransformer
{
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
            'deviceId'   => (int)$order->deviceId,
            'serviceId'  => (int)$order->serviceId,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
        ];
    }
}
