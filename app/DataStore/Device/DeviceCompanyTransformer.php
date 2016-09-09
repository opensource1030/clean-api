<?php

namespace WA\DataStore\Device;

use Dingo\Api\Transformer\FractalTransformer as DingoFractalTransformer;
use Illuminate\Pagination\Paginator as IlluminatePaginator;
use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\TransformerAbstract;
use WA\DataStore\Company\CompanyTransformer;


/**
 * Class DeviceTransformer.
 */
class DeviceCompanyTransformer extends TransformerAbstract
{
    /**
     * @param Device $device
     *
     * @return array
     */
    public function transform(DeviceCompany $devCom)
    {
        return [
            'id' => (int)$devCom->id,
            'deviceId' => $devCom->deviceId,
            'companyId' => $devCom->companyId,
            'created_at' => $devCom->created_at,
            'updated_at' => $devCom->updated_at
        ];
    }
}