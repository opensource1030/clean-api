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
class DeviceTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'assets',
    ];

    /**
     * @param Device $device
     *
     * @return array
     */
    public function transform(Device $device)
    {
        return [
            'id' => (int)$device->id,
            'identification' => $device->identification,
        ];

    }

    /**
     * @param Device $device
     *
     * @return ResourceCollection
     */
    public function includeAssets(Device $device)
    {
        return new ResourceCollection($device->assets, new AssetTransformer(),'assets');
    }

}
