<?php

namespace WA\Repositories\Permission;

use WA\Repositories\AbstractRepository;
use WA\Repositories\Role\RoleInterface;
use WA\Repositories\Permission\PermissionInterface;

class EloquentPermission extends AbstractRepository implements PermissionInterface
{
    protected $role;

    /**
     * Get the Permissions by the Role ID.
     *
     * @param int $id
     *
     * @return object of the permissions information
     */
    public function byRole($id)
    {
        $role = $this->role->byId($id);

        return $role->permissions;
    }

 /**
     * Get Permissions by the Scope ID.
     *
     * @param int $id
     *
     * @return object of the permissions information
     */
    public function getScope($id)
    {
        return $this->model->where('id', $id)->first()->scopes;
    }

    /**
     * Retrieve the filters for the Model.
     *
     * @param int  $companyId
     *
     * @return Array
     */
    public function addFilterToTheRequest($companyId) {
        //$aux['companies.id'] = (string) $companyId;
        return ''; //$aux;
    }    

    /**
     * Check if the Model and/or its relationships are related to the Company of the User.
     *
     * @param JSON  $json : The Json request.
     * @param int  $companyId
     *
     * @return Boolean
     */
    public function checkModelAndRelationships($json, $companyId) {
        return true;
    }

    /**
     * Add the attributes or the relationships needed.
     *
     * @param $data : The Data request.
     *
     * @return $data: The Data with the minimum relationship needed.
     */
    public function addRelationships($data) {
        return $data;
    }
}
