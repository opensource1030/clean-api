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
}
