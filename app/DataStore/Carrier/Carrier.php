<?php


namespace WA\DataStore\Carrier;

use WA\DataStore\BaseDataStore;

/**
 * An Eloquent Model: 'WA\DataStore\Carrier\Carrier'.
 *
 * @property int $id
 * @property string $name
 * @property bool $active
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Dump[] $dumps
 * @property-read \Illuminate\Database\Eloquent\Collection|\Client[] $clients
 * @property int $dataSourceCount
 * @property int $rawDataFilesCount
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Adjustable[] $adjustables
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Carrier\Carrier whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Carrier\Carrier whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Carrier\Carrier whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Carrier\Carrier whereRawDataFilesCount($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\User\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Carrier\Carrier[] $companies
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Asset\Asset[] $assets
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Device\Device[] $devices
 * @property-read \WA\DataStore\Location\Location $location
 * @mixin \Eloquent
 */
class Carrier extends BaseDataStore
{
    protected $table = 'carriers';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('WA\DataStore\User\User', 'userId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany('WA\DataStore\Carrier\Carrier', 'companies_carriers', 'carrierId', 'companyId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assets()
    {
        return $this->hasMany('WA\DataStore\Asset\Asset', 'carrierId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices()
    {
        return $this->hasMany('WA\DataStore\Device\Device', 'carrieId');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo('WA\DataStore\Location\Location', 'locationId');
    }

    /**
     * Get the transformer instance.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return new CarrierTransformer();
    }
}
