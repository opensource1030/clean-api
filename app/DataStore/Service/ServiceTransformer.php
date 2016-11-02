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
            'status' => $service->status,
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
            'carrierId' => $service->carrierId,
            'created_at' => $service->created_at,
            'updated_at' => $service->updated_at,
        ];
    }

        /**
     * @param Service $service
     *
     * @return ResourceCollection
     */
    public function includePackages(Service $service)
    {
        $packages = $this->applyCriteria($service->packages(), $this->criteria);

        return new ResourceCollection($packages->get(), new PackageTransformer(), 'packages');
    }

        /**
     * @param Service $service
     *
     * @return ResourceCollection
     */
    public function includeCarriers(Service $service)
    {
        $carriers = $this->applyCriteria($service->carriers(), $this->criteria);

        return new ResourceCollection($carriers->get(), new CarrierTransformer(), 'carriers');
    }
}
