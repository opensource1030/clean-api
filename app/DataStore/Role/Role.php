<?php

namespace WA\DataStore\Role;

use Zizaco\Entrust\EntrustRole;

/**
 * Class Role.
 */
class Role extends EntrustRole
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany('WA\DataStore\Permission', 'permission_role', 'role_id', 'permission_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function employees()
    {
        return $this->belongsToMany('WA\DataStore\Employee\Employee', 'role_user', 'role_id', 'user_id');
    }

    /**
     * Get the transformer instance.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return new RoleTransformer();
    }
}
