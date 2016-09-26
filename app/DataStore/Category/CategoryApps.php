<?php

namespace WA\DataStore\Category;

use WA\DataStore\BaseDataStore;

class CategoryApps extends BaseDataStore
{
    protected $table = 'categoryapps';

    protected $fillable = [
            'name'
            ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function devices()
    {
        return $this->belongsToMany('WA\DataStore\Device\Device', 'categoryapps_device', 'categoryappsId', 'deviceId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function images()
    {
        return $this->belongsToMany('WA\DataStore\Image\Image', 'categoryapps_images', 'categoryappsId', 'imageId');
    }

    /**
     * Get the transformer instance.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return new CategoryAppsTransformer();
    }
}