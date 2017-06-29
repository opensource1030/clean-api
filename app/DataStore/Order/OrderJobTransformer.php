<?php

namespace WA\DataStore\Order;

use WA\DataStore\FilterableTransformer;

/**
 * Class OrderTransformer.
 */
class OrderJobTransformer extends FilterableTransformer
{
    protected $availableIncludes = [
        'orders'
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
            'orderId'           => $order->orderId,
            'statusBefore'      => $order->statusBefore,
            'statusAfter'       => $order->statusAfter
            'created_at'        => $order->created_at,
            'updated_at'        => $order->updated_at,
        ];
    }
}
