<?php

namespace WA\DataStore\Traits;

/**
 * Class BelongsToEmployee.
 */
trait BelongsToEmployee
{
    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('WA\DataStore\Employee\Employee', 'employeeId');
    }
}
