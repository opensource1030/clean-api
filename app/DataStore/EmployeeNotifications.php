<?php

namespace WA\DataStore;

use WA\DataStore\BaseDataStore;

/**
 * Class EmployeeNotification
 *
 * @property-read \WA\DataStore\Employee\Employee $employees
 * @mixin \Eloquent
 */
class EmployeeNotifications extends BaseDataStore
{
    protected $table = 'employee_notifications';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employees()
    {
        return $this->belongsTo('WA\DataStore\Employee\Employee', 'employeeId');
    }

}