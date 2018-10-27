<?php

namespace WA\Repositories\Scope;

use WA\Repositories\AbstractRepository;
use WA\Repositories\Permission\PermissionInterface;



class EloquentScope extends AbstractRepository implements ScopeInterface
{
    protected $permission;
    
    /**
     * Get the Scopes by the Permission ID.
     *
     * @param int $id
     *
     * @return object of the permissions information
     */
    public function byPermission($id)
    {
        $permission = $this->permission->byId($id);

        return $permission->scopes;
    }
    /**
     * Get an array of all the available scopes.
     *
     * @return array of scopes
     */
    public function getAllScopes()
    {
        $scopes = $this->model->all()->toArray();

        return $scopes;
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
