<?php

namespace WA\DataStore\Category;

use League\Fractal\TransformerAbstract;

use League\Fractal\Resource\Collection as ResourceCollection;

use WA\DataStore\App\AppTransformer;
use WA\DataStore\Image\ImageTransformer;

/**
 * Class CarrierTransformer.
 */
class CategoryAppTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'apps', 'images'
    ];
    /**
     * @param CategoryApps $categoryApps
     *
     * @return array
     */
    public function transform(CategoryApp $categoryApp)
    {
        return [
            'id' => $categoryApp->id,
            'name' => $categoryApp->name,
            'created_at' => $categoryApp->created_at,
            'updated_at' => $categoryApp->updated_at
        ];
    }

    /**
     * @param CategoryApps $categoryApps
     *
     * @return ResourceCollection
     */
    public function includeApps(CategoryApp $categoryApp)
    {
        return new ResourceCollection($categoryApp->apps, new AppTransformer(),'apps');
    }

    /**
     * @param CategoryApps $categoryApps
     *
     * @return ResourceCollection
     */
    public function includeImages(CategoryApp $categoryApp)
    {
        return new ResourceCollection($categoryApp->images, new ImageTransformer(),'images');
    }
}