<?php

namespace WA\DataStore\Image;

use WA\DataStore\BaseDataStore;
use WA\DataStore\Image\ImageTransformer;

/**
 * Class Image
 *
 * @package WA\DataStore\Image
 */
class Image extends BaseDataStore
{
    protected  $table = 'images';

    protected $fillable = [
                'originalName',
                'filename',
                'size',
                'pathName',
                'update_at'];

    /**
     * Get all the owners for the Image
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * Get the transformer instance
     *
     * @return ImageTransformer
     */
    public function getTransformer()
    {
        return new ImageTransformer();
    }
}