<?php

namespace WA\Repositories\Permission;

interface PermissionsInterface
{
    /**
     * Get the Permissions by the Role ID.
     *
     * @param int $id
     *
     * @return Object of the permissions information
     */
    public function byRole($id);
}