<?php

namespace WA\DataStore\Permission;

use Zizaco\Entrust\EntrustPermission;

/**
 * Class Permission.
 */
class Permission extends EntrustPermission
{
    /**
     * Get the transformer instance.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return new PermissionTransformer();
    }
}
