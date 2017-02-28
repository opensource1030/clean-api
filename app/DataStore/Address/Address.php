<?php

namespace WA\DataStore\Address;

use WA\DataStore\BaseDataStore;

/**
 * Class Address.
 */
class Address extends BaseDataStore
{
    protected $table = 'address';

    protected $fillable = [
            'name',
            'attn',
            'phone',
            'address',
            'city',
            'state',
            'country',
            'postalCode',
            'updated_at', ];

    /**
     * Get all the owners for the address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('WA\DataStore\User\User', 'addressId');
    }

    /**
     * Get the transformer instance.
     *
     * @return AddressTransformer
     */
    public function getTransformer()
    {
        return new AddressTransformer();
    }
}
