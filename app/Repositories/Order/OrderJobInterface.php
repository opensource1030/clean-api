<?php

namespace WA\Repositories\Order;

use WA\Repositories\RepositoryInterface;

/**
 * Interface OrderInterface.
 */
interface OrderJobInterface extends RepositoryInterface
{
    /**
     * Create OrderJob.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data);

    /**
     * Update OrderJob.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data);

    /**
     * Delete OrderJob.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true);
}
