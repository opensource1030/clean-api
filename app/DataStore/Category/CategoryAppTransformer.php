<?php

namespace WA\DataStore\Category;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as ResourceCollection;
use WA\DataStore\App\AppTransformer;
use WA\DataStore\Image\ImageTransformer;
use WA\Helpers\Traits\Criteria;

/**
 * Class CarrierTransformer.
 */
class CategoryAppTransformer extends TransformerAbstract
{
    use Criteria;

    protected $availableIncludes = [
        'apps', 'images',
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
            'updated_at' => $categoryApp->updated_at,
        ];
    }

    /**
     * @param CategoryApps $categoryApps
     *
     * @return ResourceCollection
     */
    public function includeApps(CategoryApp $categoryApp)
    {
        $apps = $this->applyCriteria($categoryApp->apps(), $this->criteria);

        return new ResourceCollection($apps->get(), new AppTransformer(), 'apps');
    }

    /**
     * @param CategoryApps $categoryApps
     *
     * @return ResourceCollection
     */
    public function includeImages(CategoryApp $categoryApp)
    {
        $images = $this->applyCriteria($categoryApp->images(), $this->criteria);

        return new ResourceCollection($images->get(), new ImageTransformer(), 'images');
    }
}
