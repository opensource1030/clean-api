<?php

namespace WA\Repositories\Permission;

use WA\Repositories\AbstractRepository;
use WA\Repositories\Role\RoleInterface;


class EloquentPermission extends AbstractRepository implements PermissionsInterface
{

    protected $role;

    public function __construct(
      RoleInterface $role
    )
     {
        $this->role = $role;
    }

    /**
     * Get the Permissions by the Role ID.
     *
     * @param int $id
     *
     * @return Object of the permissions information
     */
    public function byRole($id)
    {
        $role = $this->role->byId($id);
        return $role->permissions;
    }

}