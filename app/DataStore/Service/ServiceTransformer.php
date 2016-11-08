<?php

namespace WA\DataStore\Service;

use WA\DataStore\FilterableTransformer;

/**
 * Class ServiceTransformer.
 */
class ServiceTransformer extends FilterableTransformer
{
    protected $availableIncludes = [
        'packages',
        'addons'
    ];

    protected $defaultIncludes = [
        'carriers',
    ];

    /**
     * @param Service $service
     *
     * @return array
     */
    public function transform(Service $service)
    {
        return [
            'id'                        => (int) $service->id,
            'status'                    => $service->status,            
            'title'                     => $service->title,
            'planCode'                  => $service->planCode,
            'cost'                      => $service->cost,
            'description'               => $service->description,
            'domesticMinutes'           => $service->domesticMinutes,
            'domesticData'              => $service->domesticData,
            'domesticMessages'          => $service->domesticMessages,
            'internationalMinutes'      => $service->internationalMinutes,
            'internationalData'         => $service->internationalData,
            'internationalMessages'     => $service->internationalMessages,
            'carrierId'                 => $service->carrierId,
            'created_at'                => $service->created_at,
            'updated_at'                => $service->updated_at,
        ];
    }
}
