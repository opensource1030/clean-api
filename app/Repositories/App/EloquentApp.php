<?php

namespace WA\Repositories\App;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentApp.
 */
class EloquentApp extends AbstractRepository implements AppInterface
{
    /**
     * Update App.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        $app = $this->model->find($data['id']);

        if (!$app) {
            return 'notExist';
        }

        if (isset($data['type'])) {
            $app->type = $data['type'];
        }
        if (isset($data['image'])) {
            $app->image = $data['image'];
        }
        if (isset($data['description'])) {
            $app->description = $data['description'];
        }

        if (!$app->save()) {
            return 'notSaved';
        }

        return $app;
    }

    /**
     * Get an array of all the available App.
     *
     * @return array of App
     */
    public function getAllApp()
    {
        $app = $this->model->all();

        return $app;
    }

    /**
     * Create a new app.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data)
    {
        $appData = [
            "type" =>  isset($data['type']) ? $data['type'] : null ,
            "image" => isset($data['image']) ? $data['image'] : null,
            "description" => isset($data['description']) ? $data['description'] : null,
        ];

        $app = $this->model->create($appData);

        if (!$app) {
            return false;
        }

        return $app;
    }

    /**
     * Delete a App.
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
        $aux[] = '[packages.companyId]=' . (string) $companyId . '[or][orders.companyId]=' . (string) $companyId;
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
        if(!isset($json->data->relationships)) {
            return false;
        } else {
            foreach ($json->data->relationships->packages->data as $value) {
                if ($value->type == 'packages') {
                    if ($value->companyId == $companyId) {
                        return true;
                    }
                }
            }

            foreach ($json->data->relationships->orders->data as $value) {
                if ($value->type == 'orders') {
                    if ($value->companyId == $companyId) {
                        return true;
                    }
                }
            }

            return false;
        }
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
