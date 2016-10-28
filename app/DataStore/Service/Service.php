<?php

namespace WA\DataStore\Service;

use WA\DataStore\BaseDataStore;
use WA\DataStore\Service\ServiceTransformer;

/**
 * Class Service
 *
 * @package WA\DataStore\Service
 */
class Service extends BaseDataStore
{
    protected $table = 'services';

    protected $fillable = [
        'status',
        'title',
        'planCode',
        'cost',
        'description',
        'domesticMinutes',
        'domesticData',
        'domesticMessages',
        'internationalMinutes',
        'internationalData',
        'internationalMessages',
        'carrierId',
        'updated_at'];

    /**
     * Get all the owners for the service.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * Get the transformer instance.
     *
     * @return ServiceTransformer
     */
    public function getTransformer()
    {
        return new ServiceTransformer();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function packages()
    {
        return $this->belongsToMany('WA\DataStore\Package\Package', 'package_services', 'packageId', 'servicesId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carriers()
    {
        return $this->belongsTo('WA\DataStore\Carrier\Carrier', 'carrierId');
    }
}
