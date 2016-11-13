<?php

namespace WA\DataStore\Service;

use League\Fractal\Resource\Collection as ResourceCollection;
use WA\DataStore\FilterableTransformer;
use WA\DataStore\ServiceItem\ServiceItemTransformer;

/**
 * Class ServiceTransformer.
 */
class ServiceTransformer extends FilterableTransformer
{
    protected $availableIncludes = [
        'packages',
        'serviceitems',
        'carriers'
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
            'id'          => (int)$service->id,
            'status'      => $service->status,
            'title'       => $service->title,
            'planCode'    => $service->planCode,
            'cost'        => $service->cost,
            'description' => $service->description,
            'carrierId'   => $service->carrierId,
            'created_at'  => $service->created_at,
            'updated_at'  => $service->updated_at,
        ];
    }


    public function includeServiceitems(Service $service)
    {
        $this->criteria = $this->getRequestCriteria();
        $serviceItems = $this->applyCriteria($service->serviceitems(), $this->criteria, true, [
            'serviceitems' => 'service_items'
        ]);
        return new ResourceCollection ($serviceItems->get(), new ServiceItemTransformer(), 'service_items');
    }
}
