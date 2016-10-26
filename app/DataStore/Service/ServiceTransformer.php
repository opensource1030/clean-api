<?php

namespace WA\DataStore\Service;

use League\Fractal\TransformerAbstract;

/**
 * Class ServiceTransformer.
 */
class ServiceTransformer extends TransformerAbstract
{
    /**
     * @param Service $service
     *
     * @return array
     */
    public function transform(Service $service)
    {
        return [

            'id' => (int) $service->id,
            'title' => $service->title,
            'planCode' => $service->planCode,
            'cost' => $service->cost,
            'description' => $service->description,
            'domesticMinutes' => $service->domesticMinutes,
            'domesticData' => $service->domesticData,
            'domesticMessages' => $service->domesticMessages,
            'internationalMinutes' => $service->internationalMinutes,
            'internationalData' => $service->internationalData,
            'internationalMessages' => $service->internationalMessages,
            'created_at' => $service->created_at,
            'updated_at' => $service->updated_at,
        ];
    }
}
