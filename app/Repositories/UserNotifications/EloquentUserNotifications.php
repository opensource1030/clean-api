<?php

namespace WA\Repositories\UserNotifications;

use WA\Repositories\AbstractRepository;

class EloquentUserNotifications extends AbstractRepository implements UserNotificationsInterface
{
    /**
     * Return by categoryId.
     *
     * @param $categoryId
     *
     * @return mixed
     */
    public function byCategoryId($categoryId)
    {
        $response = $this->model->where('id', $categoryId);

        return $response->get();
    }

    /**
     * Get Notification Types based on CategoryId.
     *
     * @param $categoryId
     *
     * @return mixed
     */
    public function getDistinctNotificationTypesByCategory($categoryId)
    {
        $response = $this->model->distinct()->select('type')->where('categoryId', $categoryId);

        return $response->get(['type'])->toArray();
    }

    /**
     * Get Users by Category and Type of Notification.
     *
     * @param $type
     * @param $categoryId
     *
     * @return mixed
     */
    public function byTypeAndCategoryId($type, $categoryId)
    {
        $response = $this->model->select('employeeId')->where('categoryId', $categoryId)->where('type', $type);

        return $response->get(['employeeId'])->toArray();
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
        return '';//$aux;
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
