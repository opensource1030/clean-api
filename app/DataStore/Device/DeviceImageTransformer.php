<?php

namespace WA\DataStore\Device;

use Dingo\Api\Transformer\FractalTransformer as DingoFractalTransformer;
use Illuminate\Pagination\Paginator as IlluminatePaginator;
use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\TransformerAbstract;
use WA\DataStore\Image\ImageTransformer;


/**
 * Class DeviceTransformer.
 */
class DeviceImageTransformer extends TransformerAbstract
{
    /**
     * @param Device $device
     *
     * @return array
     */
    public function transform(DeviceImage $devImg)
    {
        return [
            'id' => (int)$devImg->id,
            'deviceId' => $devImg->deviceId,
            'imageId' => $devImg->imageId,
            'created_at' => $devImg->created_at,
            'updated_at' => $devImg->updated_at
        ];
    }
}