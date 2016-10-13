<?php

namespace WA\DataStore\App;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item as ResourceItem;
use League\Fractal\TransformerAbstract;

/**
 * Class AppTransformer
 *
 */
class AppTransformer extends TransformerAbstract
{
    /**
     * @param App $app
     *
     * @return array
     */
    public function transform(App $app)
    {
        return [

            'id' => (int)$app->id,
            'type' => $app->type,
            'image' => $app->image,
            'description' => $app->description,
            'created_at' => $app->created_at,
            'updated_at' => $app->updated_at
        ];
    }
}