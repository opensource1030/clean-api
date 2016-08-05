<?php

namespace WA\DataStore\UdlValuePathEmployees;

use WA\DataStore\BaseDataStore;

/**
 * WA\DataStore\UdlValuePathEmployees\UdlValuePathEmployees.
 *
 * @property int $id
 * @property string $udlValuePathId
 * @property int $creatorId
 * @property string $userEmail
 * @property string $userFirstName
 * @property string $userLastName
 * @property string $userEmployeeId
 */

class UdlValuePathEmployees extends BaseDataStore
{
    protected $table = 'udl_value_paths_creators_users';
    protected $fillable = ['udlValuePathId', 'creatorId','userEmail', 'userFirstName', 'userLastName', 'userEmployeeId' ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function employees()
    {
        return $this->hasMany('WA\DataStore\Employee\Employee', 'creatorId');
    }


}
