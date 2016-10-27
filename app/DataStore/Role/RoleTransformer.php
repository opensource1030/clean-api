<?php

namespace WA\DataStore\Role;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item as ResourceItem;
use League\Fractal\TransformerAbstract;

/**
 * Class RoleTransformer.
 */
class RoleTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'permissions',
    ];

    /**
     * @param Role $role
     *
     * @return array
     */
    public function transform(Role $role)
    {
        return [
            'id' => (int) $role->id,
            'name' => $role->display_name,
        ];
    }
}
