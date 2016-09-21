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
    protected  $table = 'packages';

    protected $fillable = [
            'name',
            'updated_at'];

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function conditions()
    {
        return $this->belongsToMany('WA\DataStore\Condition\Condition', 'package_conditions', 'packageId', 'conditionsId');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function services()
    {
        return $this->belongsToMany('WA\DataStore\Company\Company', 'package_services', 'packageId', 'servicesId');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function devices()
    {
        return $this->belongsToMany('WA\DataStore\Company\Company', 'package_devices', 'packageId', 'devicesId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function apps()
    {
        return $this->belongsToMany('WA\DataStore\Company\Company', 'package_apps', 'packageId', 'appsId');
    }
}