<?php

namespace WA\Repositories\Request;

use WA\Repositories\RepositoryInterface;

/**
 * Interface RequestInterface
 *
 * @package WA\Repositories\Request
 */
interface RequestInterface extends RepositoryInterface
{
    /**
     * Get Array of all Requests.
     *
     * @return Array of Request
     */
    public function getAllRequest();

    /**
     * Create Request
     *
     * @param array $data
     * @return bool|static
     */
    public function create(array $data);

    /**
     * Update Request.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data);

    /**
     * Delete Request.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true);

}