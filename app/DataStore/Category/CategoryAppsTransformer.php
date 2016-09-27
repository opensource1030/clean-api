<?php

namespace WA\DataStore\Category;

use League\Fractal\TransformerAbstract;

use League\Fractal\Resource\Collection as ResourceCollection;

use WA\DataStore\Device\DeviceTransformer;

/**
 * Class CarrierTransformer.
 */
class CategoryAppsTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'apps'
    ];
    /**
     * @param CategoryApps $categoryApps
     *
     * @return array
     */
    public function transform(CategoryApps $categoryApps)
    {
        return [
            'id' => $categoryApps->id,
            'name' => $categoryApps->name
        ];
    }

    /**
     * @param CategoryApps $categoryApps
     *
     * @return ResourceCollection
     */
    public function includeApps(CategoryApps $categoryApps)
    {
        return new ResourceCollection($categoryApps->apps, new AppTransformer(),'apps');
    }

    /**
     * @param CategoryApps $categoryApps
     *
     * @return ResourceCollection
     */
    public function includeImages(CategoryApps $categoryApps)
    {
        return new ResourceCollection($categoryApps->images, new ImageTransformer(),'images');
    }
}