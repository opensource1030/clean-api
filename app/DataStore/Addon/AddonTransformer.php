<?php

namespace WA\DataStore\Addon;

use WA\DataStore\FilterableTransformer;

/**
 * Class AddonTransformer.
 */
class AddonTransformer extends FilterableTransformer
{
    /**
     * @param Addon $addon
     *
     * @return array
     */
    public function transform(Addon $addon)
    {
        return [

            'id'          => (int)$addon->id,
            'name'        => $addon->name,
            'cost'        => $addon->cost,
            'serviceId'   => $addon->serviceId,
            'created_at'  => $addon->created_at,
            'updated_at'  => $addon->updated_at,
        ];
    }
}
