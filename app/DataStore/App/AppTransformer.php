<?php

namespace WA\DataStore\App;

use WA\DataStore\FilterableTransformer;
use League\Fractal\Resource\Collection as ResourceCollection;

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

    public function includeOrders(App $app)
    {
        $this->criteria = $this->getRequestCriteria();
        $orders = $this->applyCriteria($app->orders(), $this->criteria, true, [
            'orders' => 'orders'
        ]);
        return new ResourceCollection ($orders->get(), new OrderTransformer(), 'orders');
    }
}
