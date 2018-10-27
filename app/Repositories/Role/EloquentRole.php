<?php

namespace WA\Repositories\Role;

use Illuminate\Database\Eloquent\Model;
use WA\Repositories\AbstractRepository;

class EloquentRole extends AbstractRepository implements RoleInterface
{
    /**
     * Get an array of all the available roles.
     *
     * @return array of roles
     */
    public function getAllRoles()
    {
        $roles = $this->model->all()->toArray();

        return $roles;
    }

    /**
     * Get Permissions by the Role ID.
     *
     * @param int $id
     *
     * @return object of the permissions information
     */
    public function getPermissions($id)
    {
        return $this->model->where('id', $id)->first()->permissions;
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
