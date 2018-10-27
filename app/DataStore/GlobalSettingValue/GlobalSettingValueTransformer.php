<?php

namespace WA\DataStore\GlobalSettingValue;

use WA\DataStore\FilterableTransformer;

/**
 * Class GlobalSettingValueTransformer.
 */
class GlobalSettingValueTransformer extends FilterableTransformer
{
    protected $availableIncludes = [
        'globalsettings',
        'companies'
    ];

    /**
     * @param GlobalSettingValue $globalSettingValue
     *
     * @return array
     */
    public function transform(GlobalSettingValue $globalSettingValue)
    {
        return [
            'id'                => (int)$globalSettingValue->id,
            'name'              => $globalSettingValue->name,
            'label'             => $globalSettingValue->label,
            'globalSettingId'   => $globalSettingValue->globalSettingId,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function globalsettings()
    {
        return $this->hasMany('WA\DataStore\GlobalSetting\GlobalSetting', 'globalSettingId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany('WA\DataStore\Company\Company', 'company_settings', 'companyId', 'globalSettingsValueId');
    }
}
