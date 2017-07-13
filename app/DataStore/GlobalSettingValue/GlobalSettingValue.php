<?php

namespace WA\DataStore\GlobalSettingValue;

use WA\DataStore\BaseDataStore;

/**
 * Class GlobalSettingValue.
 */
class GlobalSettingValue extends BaseDataStore
{
    protected $table = 'global_settings_values';

    public $timestamps = false;

    protected $fillable = [
            'name',
            'label',
            'globalSettingId' ];

    /**
     * Get the transformer instance.
     *
     * @return GlobalSettingValueTransformer
     */
    public function getTransformer()
    {
        return new GlobalSettingValueTransformer();
    }

    public function globalsettings()
    {
        return $this->belongsTo('WA\DataStore\GlobalSetting\GlobalSetting', 'globalSettingId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function companies()
    {
        return $this->belongsToMany('WA\DataStore\Company\Company', 'company_settings', 'globalSettingsValueId', 'companyId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function packages()
    {
        return $this->belongsToMany('WA\DataStore\Package\Package', 'package_settings', 'globalSettingsValueId', 'packageId');
    }
}
