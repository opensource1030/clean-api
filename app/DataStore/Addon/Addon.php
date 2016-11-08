<?php

namespace WA\DataStore\Addon;

use WA\DataStore\BaseDataStore;

/**
 * Class App.
 */
class Addon extends BaseDataStore
{
    protected $table = 'addons';

    protected $fillable = [
            'name',
            'cost',
            'serviceId',
            'updated_at', ];

    /**
     * Get the transformer instance.
     *
     * @return AddonTransformer
     */
    public function getTransformer()
    {
        return new AddonTransformer();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo('WA\DataStore\Service\Service', 'serviceId');
    }
}
