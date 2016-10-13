<?php

namespace WA\DataStore\Price;

use Dingo\Api\Transformer\FractalTransformer as DingoFractalTransformer;
use Illuminate\Pagination\Paginator as IlluminatePaginator;
use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\TransformerAbstract;


/**
 * Class DeviceTransformer.
 */
class PriceTransformer extends TransformerAbstract
{
    /**
     * @param Device $device
     *
     * @return array
     */
    public function transform(Price $price)
    {
        return [
            'id' => (int)$price->id,
            'priceRetail' => $price->priceRetail,
            'price1' => $price->price1,
            'price2' => $price->price2,
            'priceOwn' => $price->priceOwn,
            'deviceId' => $price->deviceId,
            'capacityId' => $price->capacityId,
            'styleId' => $price->styleId,
            'carrierId' => $price->carrierId,
            'companyId' => $price->companyId,
            'created_at' => $price->created_at,
            'updated_at' => $price->updated_at
        ];
    }
}