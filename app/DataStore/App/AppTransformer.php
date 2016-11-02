<?php

namespace WA\DataStore\App;

use WA\DataStore\FilterableTransformer;

/**
 * Class AppTransformer.
 */
class AppTransformer extends FilterableTransformer
{
    /**
     * @param App $app
     *
     * @return array
     */
    public function transform(App $app)
    {
        return [

            'id'          => (int)$app->id,
            'type'        => $app->type,
            'image'       => $app->image,
            'description' => $app->description,
            'created_at'  => $app->created_at,
            'updated_at'  => $app->updated_at,
        ];
    }
}
