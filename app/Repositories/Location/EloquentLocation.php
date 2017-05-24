<?php

namespace WA\Repositories\Location;

use WA\Repositories\AbstractRepository;

class EloquentLocation extends AbstractRepository implements LocationInterface
{
    /**
     * Get location details by name.
     *
     * @param $name
     *
     * @return mixed
     */
    public function byName($name)
    {
        return $this->model->where('name', $name)->first();
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
}
