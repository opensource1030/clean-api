<?php

namespace WA\Repositories\UdlValuePath;

use Illuminate\Database\Eloquent\Model;
use WA\Repositories\AbstractRepository;
use DB;

class EloquentUdlValuePath extends AbstractRepository implements UdlValuePathInterface
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
     * Get the ExternalId value that matches the UDLPath.
     *
     * @param string $udlValuePath
     *
     * @return object object of the udl information
     */
    public function byUdlPath($udlValuePath)
    {
        $response = $this->model->where('udlPath', $udlValuePath);

        return $response->first();
    }

    /**
     * Get the ExternalId value that matches the UDLPath Id.
     *
     * @param int $udlValuePathId
     *
     * @return object object of the udl information
     */
    public function byUdlId($udlValuePathId)
    {
        $response = $this->model->where('id', $udlValuePathId);

        return $response->first();
    }

    public function byExternalId($externalId)
    {
        $response = $this->model->where('externalId', $externalId);

        return $response->first();
    }

    /**
     * Get the maximum value of the external ID.
     *
     * @return int
     */
    public function getMaxExternalId()
    {
        return  $this->model->select(DB::raw(' MAX(CONVERT(externalId, SIGNED INTEGER)) as externalId'))->get('externalId');
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
