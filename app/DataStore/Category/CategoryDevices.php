<?php

namespace WA\DataStore\Category;

use WA\DataStore\BaseDataStore;

class CategoryDevices extends BaseDataStore
{
    protected $table = 'categorydevices';

    protected $fillable = [
            'name'
            ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function devices()
    {
        return $this->belongsToMany('WA\DataStore\Device\Device', 'categorydevices_device', 'categorydevicesId', 'deviceId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function images()
    {
        return $this->belongsToMany('WA\DataStore\Image\Image', 'categorydevices_images', 'categorydevicesId', 'imageId');
    }

    /**
     * Get the transformer instance.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return new CategoryDevicesTransformer();
    }
}