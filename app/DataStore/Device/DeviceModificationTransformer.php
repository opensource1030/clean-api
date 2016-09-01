<?php

namespace WA\DataStore\Device;

use Dingo\Api\Transformer\FractalTransformer as DingoFractalTransformer;
use Illuminate\Pagination\Paginator as IlluminatePaginator;
use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\TransformerAbstract;
use WA\DataStore\Asset\AssetTransformer;


/**
 * Class DeviceTransformer.
 */
class DeviceModificationTransformer extends TransformerAbstract
{
    /**
     * @param Device $device
     *
     * @return array
     */
    public function transform(DeviceModification $devMod)
    {
        return [
            'id' => (int)$devMod->id,
            'deviceId' => $devMod->deviceId,
            'modificationId' => $devMod->modificationId,
            'created_at' => $device->created_at,
            'updated_at' => $device->updated_at
        ];
    }
}