<?php

namespace WA\Repositories\Service;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentService.
 *
 * @package WA\Repositories\Service
 */
class EloquentService extends AbstractRepository implements ServiceInterface
{
    /**
     * Update Service.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        $service = $this->model->find($data['id']);

        if (!$service) {
            return 'notExist';
        }

        if (isset($data['status'])) {
            $service->status = $data['status'];
        }
        if (isset($data['title'])) {
            $service->title = $data['title'];
        }
        if (isset($data['planCode'])) {
            $service->planCode = $data['planCode'];
        }
        if (isset($data['cost'])) {
            $service->cost = $data['cost'];
        }
        if (isset($data['description'])) {
            $service->description = $data['description'];
        }
        if (isset($data['currency'])) {
            $service->currency = $data['currency'];
        }
        if (isset($data['carrierId'])) {
            $service->carrierId = $data['carrierId'];
        }
        if (isset($data['companyId'])) {
            $service->companyId = $data['companyId'];
        }


        if (!$service->save()) {
            return 'notSaved';
        }

        return $service;
    }

    /**
     * Get an array of all the available service.
     *
     * @return array of Service
     */
    public function getAllservice()
    {
        $service = $this->model->all();

        return $service;
    }

    /**
     * Create a new Service.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data)
    {
        if (!isset($data['companyId'])) {
            return false;
        }

        $serviceData = [
            "status" =>  isset($data['status']) ? $data['status'] : null ,
            "title" =>  isset($data['title']) ? $data['title'] : null ,
            "planCode" => isset($data['planCode']) ? $data['planCode'] : 0,
            "cost" =>  isset($data['cost']) ? $data['cost'] : 0,
            "description" => isset($data['description']) ? $data['description'] : '',
            "currency" => isset($data['currency']) ? $data['currency'] : 'USD',
            "carrierId" =>  isset($data['carrierId']) ? $data['carrierId'] : null ,
            "companyId" =>  isset($data['companyId']) ? $data['companyId'] : null ,
        ];

        $service = $this->model->create($serviceData);

        if (!$service) {
            return false;
        }

        return $service;
    }

    /**
     * Delete a Service.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true)
    {
        if (!$this->model->find($id)) {
            return false;
        }

        if (!$soft) {
            $this->model->forceDelete($id);
        }

        return $this->model->destroy($id);
    }

    /**
     * Retrieve the filters for the Model.
     *
     * @param int  $companyId
     *
     * @return Array
     */
    public function addFilterToTheRequest($companyId) {
        $aux['companyId'] = (string) $companyId;
        return $aux;
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
        return $json['attributes']['companyId'] == $companyId;
    }

    /**
     * Add the attributes or the relationships needed.
     *
     * @param $data : The Data request.
     *
     * @return $data: The Data with the minimum relationship needed.
     */
    public function addRelationships($data) {
        if (!isset($data->attributes->companyId)) {
            $data['attributes']['companyId'] = \Auth::user()->companyId;    
        }

        return $data;
    }
}
