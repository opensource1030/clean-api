<?php

namespace WA\DataStore\Category;

use WA\DataStore\FilterableTransformer;

/**
 * Class CarrierTransformer.
 */
class PresetTransformer extends FilterableTransformer
{

    protected $availableIncludes = [
        'devices',
        'images',
    ];

    /**
     * @param Preset $preset
     *
     * @return array
     */
    public function transform(Preset $preset)
    {
        return [
            'id'         => $preset->id,
            'name'       => $preset->name,
            'created_at' => $preset->created_at,
            'updated_at' => $preset->updated_at,
        ];
    }

}
