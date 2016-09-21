<?php

namespace WA\DataStore\Device;

use Dingo\Api\Transformer\FractalTransformer as DingoFractalTransformer;
use Illuminate\Pagination\Paginator as IlluminatePaginator;
use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\TransformerAbstract;
use WA\DataStore\Carrier\CarrierTransformer;


/**
 * Class DeviceTransformer.
 */
class DeviceCarrierTransformer extends TransformerAbstract
{
    /**
     * @param Device $device
     *
     * @return array
     */
    public function transform(DeviceCarrier $devCar)
    {
        return [
            'id' => (int)$devCar->id,
            'deviceId' => $devCar->deviceId,
            'carrierId' => $devCar->carrierId,
            'created_at' => $devCar->created_at,
            'updated_at' => $devCar->updated_at
        ];
    }
}