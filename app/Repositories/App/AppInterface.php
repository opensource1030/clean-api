<?php

namespace WA\Repositories\App;

use WA\Repositories\RepositoryInterface;

/**
 * Interface AppInterface
 *
 * @package WA\Repositories\App
 */
interface AppInterface extends RepositoryInterface
{
    /**
     * Get Array of all Apps.
     *
     * @return Array of App
     */
    public function getAllApp();

    /**
     * Create App
     *
     * @param array $data
     * @return bool|static
     */
    public function create(array $data);

    /**
     * Update App.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data);

    /**
     * Delete App.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true);

}