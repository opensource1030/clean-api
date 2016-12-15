<?php

namespace WA\DataStore\DeviceVariation;

use WA\DataStore\MutableDataStore;

class DeviceVariation extends MutableDataStore
{
    protected $table = 'device_variations';

    public $timestamps = true;

    protected $fillable = [
        'priceRetail',
        'price1',
        'price2',
        'priceOwn',
        'deviceId',
        'carrierId',
        'companyId',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function modifications()
    {
        return $this->belongsToMany('WA\DataStore\Modification\Modification', 'device_variations_modifications', 'deviceVariationId', 'modificationId');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function presets()
    {
        return $this->belongsToMany('WA\DataStore\Preset\Preset', 'preset_deviceVariations', 'presetId', 'deviceVariationId');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carriers()
    {
        return $this->belongsTo('WA\DataStore\Carrier\Carrier', 'carrierId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function companies()
    {
        return $this->belongsTo('WA\DataStore\Company\Company', 'companyId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function devices()
    {
        return $this->belongsTo('WA\DataStore\Device\Device', 'deviceId');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function assets()
    {
        return $this->hasMany('WA\DataStore\Asset\Asset', 'deviceVariationId');
    }

    /**
     * Get the transformer instance.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return new DeviceVariationTransformer();
    }
}
