<?php

namespace WA\DataStore\Order;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item as ResourceItem;
use League\Fractal\TransformerAbstract;

/**
 * Class OrderTransformer
 *
 */
class OrderTransformer extends TransformerAbstract
{

    /**
     * @param Order $order
     *
     * @return array
     */
    public function transform(Order $order)
    {
        return [

            'id' => (int)$order->id,

            'status' => $order->status,

            'created_at' => $order->created_at,

            'idEmployee' => $order->idEmployee,

            'idPackage' => (int)$order->idPackage,
        ];
    }
}