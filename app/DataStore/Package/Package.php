<?php

namespace WA\DataStore\Package;

use WA\DataStore\BaseDataStore;
use WA\DataStore\Package\PackageTransformer;

/**
 * Class Package
 *
 * @package WA\DataStore\Package
 */
class Package extends BaseDataStore
{
    protected  $table = 'package';

    protected $fillable = array('name');


    /**
     * Get all the owners for the package
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
     * @return PackageTransformer
     */
    public function getTransformer()
    {
        return new PackageTransformer();
    }

}