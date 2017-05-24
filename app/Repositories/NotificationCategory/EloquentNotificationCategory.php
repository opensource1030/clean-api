<?php

namespace WA\Repositories\NotificationCategory;

use WA\Repositories\AbstractRepository;

class EloquentNotificationCategory extends AbstractRepository implements NotificationCategoryInterface
{
    /**
     * Return Notification Categories by Category Id.
     *
     * @param $categoryId
     *
     * @return mixed
     */
    public function byCategory($categoryId)
    {
        $response = $this->model->where('id', $categoryId);

        return $response->get();
    }

    /**
     * Return Notification Categories by Category Name.
     *
     * @param $name
     *
     * @return mixed
     */
    public function byName($name)
    {
        $response = $this->model->where('name', $name);

        return $response->first();
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
