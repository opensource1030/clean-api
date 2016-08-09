<?php


namespace WA\DataStore\Device;

use WA\DataStore\MutableDataStore;
use WA\DataStore\Traits\BelongsToDeviceType;
use WA\DataStore\Traits\BelongsToJobStatus;

/**
 * An Eloquent Model: 'WA\DataStore\Device\Device'.
 *
 * @property int $id
 * @property int $type
 * @property string $identification
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Asset\Asset[] $assets
 * @property int $deviceTypeId
 * @property-read \WA\DataStore\DeviceType $deviceType
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Device\Device whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Device\Device whereIdentification($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Device\Device whereDeviceTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Device\Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Device\Device whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Attribute[] $attributes
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\User\User[] $users
 * @property-read \WA\DataStore\Carrier\Carrier $carrier
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Company\Company[] $companies
 * @property-read \WA\DataStore\SyncJob $sync
 * @property-read \WA\DataStore\JobStatus $jobstatus
 * @mixin \Eloquent
 */
class Device extends MutableDataStore
{
    protected $table = 'devices';

    use BelongsToJobStatus;
    use BelongsToDeviceType;

    public $timestamps = true;

    protected $fillable = [
        'deviceTypeId',
        'statusId',
        'externalId',
        'identification',
        'syncId',
        'carrierId',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function assets()
    {
        return $this->belongsToMany('WA\DataStore\Asset\Asset', 'asset_devices', 'deviceId', 'assetId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('WA\DataStore\User\User', 'employee_devices', 'deviceId', 'employeeId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carrier()
    {
        return $this->belongsTo('WA\DataStore\Carrier\Carrier', 'carrierId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany('WA\DataStore\Company\Company', 'companies_devices', 'deviceId', 'companyId');
    }

    /**
     * @return $this
     */
    public function attributes()
    {
        return $this->morphToMany('WA\DataStore\Attribute', 'attributable')->withPivot(['value', 'dataOriginationId']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sync()
    {
        return $this->belongsTo('WA\DataStore\SyncJob', 'statusId');
    }

    /**
     * Get the transformer instance.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return new DeviceTransformer();
    }
}