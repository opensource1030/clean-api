<?php

namespace WA\DataStore\GlobalSetting;

use WA\DataStore\BaseDataStore;

/**
 * Class GlobalSetting.
 */
class GlobalSetting extends BaseDataStore
{
    protected $table = 'global_settings';

    public $timestamps = false;

    protected $fillable = [
            'name',
            'label',
            'description' ];

    /**
     * Get the transformer instance.
     *
     * @return GlobalSettingTransformer
     */
    public function getTransformer()
    {
        return new GlobalSettingTransformer();
    }

    public function globalsettingvalues()
    {
        return $this->hasMany('WA\DataStore\GlobalSettingValue\GlobalSettingValue', 'globalSettingId');
    }
}
