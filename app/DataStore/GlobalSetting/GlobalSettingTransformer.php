<?php

namespace WA\DataStore\GlobalSetting;

use WA\DataStore\FilterableTransformer;

/**
 * Class GlobalSettingTransformer.
 */
class GlobalSettingTransformer extends FilterableTransformer
{
    protected $availableIncludes = [
        'globalsettingvalues'
    ];

    /**
     * @param GlobalSetting $globalSetting
     *
     * @return array
     */
    public function transform(GlobalSetting $globalSetting)
    {
        return [
            'id'            => (int)$globalSetting->id,
            'name'          => $globalSetting->name,
            'label'         => $globalSetting->label,
            'description'   => $globalSetting->description,
        ];
    }
}
