<?php


namespace WA\DataStore\Device;

use WA\DataStore\MutableDataStore;

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
class DeviceModification extends MutableDataStore
{
    protected $table = 'device_modifications';

    public $timestamps = true;

    protected $fillable = [
        'deviceId',
        'modificationId',
    ];

    /**
     * Get the transformer instance.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return new DeviceModificationTransformer();
    }
}
