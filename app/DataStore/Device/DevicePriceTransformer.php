<?php

namespace WA\DataStore\Device;

use Dingo\Api\Transformer\FractalTransformer as DingoFractalTransformer;
use Illuminate\Pagination\Paginator as IlluminatePaginator;
use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\TransformerAbstract;
use WA\DataStore\Price\PriceTransformer;


/**
 * Class DeviceTransformer.
 */
class DevicePriceTransformer extends TransformerAbstract
{
    /**
     * @param Device $device
     *
     * @return array
     */
    public function transform(DevicePrice $devPri)
    {
        return [
            'id' => (int)$devPri->id,
            'priceRetail' => $devPri->priceRetail,
            'price1' => $devPri->price1,
            'price2' => $devPri->price2,
            'priceOwn' => $devPri->priceOwn,
            'deviceId' => $devPri->deviceId,
            'capacityId' => $devPri->capacityId,
            'styleId' => $devPri->styleId,
            'carrierId' => $devPri->carrierId,
            'companyId' => $devPri->companyId,
            'created_at' => $devPri->created_at,
            'updated_at' => $devPri->updated_at
        ];
    }
}