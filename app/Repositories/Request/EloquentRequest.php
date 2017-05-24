<?php

namespace WA\Repositories\Request;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentRequest.
 */
class EloquentRequest extends AbstractRepository implements RequestInterface
{
    /**
     * Update Request.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        $request = $this->model->find($data['id']);

        if (!$request) {
            return false;
        }

        if (isset($data['name'])) {
            $request->name = $data['name'];
        }
        if (isset($data['description'])) {
            $request->description = $data['description'];
        }

        if (!$request->save()) {
            return false;
        }

        return $request;
    }

    /**
     * Get an array of all the available Request.
     *
     * @return array of request
     */
    public function getAllRequest()
    {
        $request = $this->model->all();

        return $request;
    }

    /**
     * Create a new Request.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data)
    {
        $requestData = [
            'name' => isset($data['name']) ? $data['name'] : null,
            'description' => isset($data['description']) ? $data['description'] : null,
        ];

        $request = $this->model->create($requestData);

        if (!$request) {
            return false;
        }

        return $request;
    }

    /**
     * Delete a Request.
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
