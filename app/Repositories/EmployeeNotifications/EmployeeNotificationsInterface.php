<?php

namespace WA\Repositories\EmployeeNotifications;

use WA\Repositories\RepositoryInterface;

interface EmployeeNotificationsInterface extends RepositoryInterface
{
    /**
     * Return by categoryId
     *
     * @param $categoryId
     * @return mixed
     */
    public function byCategoryId($categoryId);

    /**
     * Get Notification Types based on CategoryId
     *
     * @param $categoryId
     * @return mixed
     */
    public function getDistinctNotificationTypesByCategory($categoryId);

    /**
     * Get Users by Category and Type of Notification
     *
     * @param $type
     * @param $categoryId
     * @return mixed
     */
    public function byTypeAndCategoryId($type, $categoryId);
}