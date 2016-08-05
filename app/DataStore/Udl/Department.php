<?php

namespace WA\DataStore\Udl;

use WA\DataStore\BaseDataStore;

/**
 * Class Department.
 */
class Department extends BaseDataStore
{
    protected $table = 'udl_values';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->hasMany('WA\DataStore\Employee\Employee', 'departmentId');
    }
}
