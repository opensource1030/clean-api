<?php

namespace WA\Repositories\Request;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentRequest
 *
 * @package WA\Repositories\Request
 */
class EloquentRequest extends AbstractRepository implements RequestInterface
{
    /**
     * Update Request
     *
     * @param array $data
     * @return bool
     */
    public function update(array $data)
    {
        $request = $this->model->find($data['id']);

        if(!$request)
        {
            return false;
        }

        $request->name =  isset($data['name']) ? $data['name'] : null ;
        $request->description = isset($data['description']) ? $data['description'] : null;

        if(!$request->save()) {
            return false;
        }

        return $request;

    }

    /**
     * Get an array of all the available Request.
     *
     * @return Array of request
     */
    public function getAllRequest()
    {
        $request =  $this->model->all();
        return $request;
    }

    /**
     * Create a new Request
     *
     * @param array $data
     * @return bool|static
     */
    public function create(array $data)
    {

        $requestData = [
            "name" =>  isset($data['name']) ? $data['name'] : null ,
            "description" => isset($data['description']) ? $data['description'] : null,
        ];

        $request = $this->model->create($requestData);

        if(!$request) {
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
}