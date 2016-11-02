<?php

namespace WA\DataStore\Category;

use WA\DataStore\BaseDataStore;

class Preset extends BaseDataStore
{
    protected $table = 'presets';

    protected $fillable = [
            'name',
            ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function devices()
    {
        return $this->belongsToMany('WA\DataStore\Device\Device', 'preset_devices', 'presetId', 'deviceId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function images()
    {
        return $this->belongsToMany('WA\DataStore\Image\Image', 'preset_images', 'presetId', 'imageId');
    }

    /**
     * Get the transformer instance.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return new PresetTransformer();
    }
}
