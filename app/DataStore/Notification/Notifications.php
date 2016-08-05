<?php

namespace WA\DataStore\Notification;

use Illuminate\Database\Eloquent\SoftDeletes as SoftDeletingTrait;
use WA\DataStore\BaseDataStore;
use WA\DataStore\MutableDataStore;

/**
 * WA\DataStore\Notification\Notifications
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Employee\Employee[] $employees
 * @mixin \Eloquent
 */
class Notifications extends MutableDataStore
{

    protected $table = 'notifications';

    protected $fillable = [
        'from_id',
        'from_type',
        'to_id',
        'to_type',
        'category_id',
        'url',
        'extra',
        'read',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function employees()
    {
        return $this->belongsToMany('WA\DataStore\Employee\Employee', 'employee_assets', 'assetId', 'employeeId');
    }

}