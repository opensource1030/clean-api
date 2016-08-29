<?php

namespace WA\DataStore\Request;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item as ResourceItem;
use League\Fractal\TransformerAbstract;

/**
 * Class RequestTransformer
 *
 */
class RequestTransformer extends TransformerAbstract
{

    /**
     * @param Request $Request
     *
     * @return array
     */
    public function transform(Request $request)
    {
        return [

            'id' => (int)$request->id,

            'name' => $request->name,

            'description' => $request->description,
        ];
    }
}