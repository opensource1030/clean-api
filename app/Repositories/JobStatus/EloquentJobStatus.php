<?php

namespace WA\Repositories\JobStatus;

use Illuminate\Database\Eloquent\Model;

class EloquentJobStatus implements JobStatusInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get the job id by it's name.
     *
     * @param $name
     *
     * @return int id | null
     */
    public function idByName($name)
    {
        $model = $this->model->where('name', $name);

        if (!$model) {
            return;
        }

        return $model->pluck('id');
    }

    /**
     * Get the status name by its ID.
     *
     * @param $id
     *
     * @return string $name | null
     */
    public function nameById($id)
    {
        $model = $this->model->where('id', $id);

        if (!$model) {
            return;
        }

        return $model->pluck('name');
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
