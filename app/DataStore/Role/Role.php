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
    public function users()
    {
        return $this->belongsToMany('WA\DataStore\User\User', 'role_user', 'role_id', 'user_id');
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

    /**
     * @return array
     */
    public function getTableColumns()
    {
        return \Cache::remember($this->getTable() . '-columns', 5, function () {
            return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        });
    }
}
