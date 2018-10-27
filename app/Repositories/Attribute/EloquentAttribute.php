<?php

namespace WA\Repositories\Attribute;

use Illuminate\Database\Eloquent\Model;
use WA\Repositories\Traits\GetArray;

class EloquentAttribute implements AttributeInterface
{
    protected $model;

    use GetArray;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get the attribute information by it's name.
     *
     * @param $name
     *
     * @return object object
     */
    public function byName($name)
    {
        return $this->model->where('name', $name)
            ->first();
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
