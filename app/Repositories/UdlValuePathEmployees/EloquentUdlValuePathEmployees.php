<?php

namespace WA\Repositories\UdlValuePathEmployees;

use Illuminate\Database\Eloquent\Model;
use WA\Repositories\AbstractRepository;

class EloquentUdlValuePathEmployees extends AbstractRepository implements UdlValuePathEmployeesInterface
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
     * Get the ExternalId value that matches the UDLValuePathId.
     *
     * @param string $udlValuePathId
     *
     * @return Object object of the udlValuePathEmployees information
     */
    public function byUdlPathId($udlValuePathId)
    {
        $response = $this->model->where('udlValuePathId', $udlValuePathId);

        return $response->first();
    }

    /**
     * Get the ExternalId value that matches the creator Id.
     *
     * @param int $creatorId
     *
     * @return Object object of the udlValuePathEmployees information
     */
    public function byCreatorId($creatorId)
    {
        $response = $this->model->where('creatorId', $creatorIdId);

        return $response->first();
    }
}
