<?php

namespace WA\DataStore\Package;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item as ResourceItem;
use League\Fractal\TransformerAbstract;

/**
 * Class PackageTransformer
 *
 */
class PackageTransformer extends TransformerAbstract
{

    /**
     * @param Package $Package
     *
     * @return array
     */
    public function transform(Package $package)
    {
        return [

            'id' => (int)$package->id,

            'name' => $package->name,
        ];
    }
}