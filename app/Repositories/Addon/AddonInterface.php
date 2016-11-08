<?php

namespace WA\Repositories\Addon;

use WA\Repositories\RepositoryInterface;

/**
 * Interface AddonInterface.
 */
interface AddonInterface extends RepositoryInterface
{
    /**
     * Get Array of all Addons.
     *
     * @return array of Addon
     */
    public function getAllAddon();

    /**
     * Create Addon.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data);

    /**
     * Update Addon.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data);

    /**
     * Delete Addon.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true);
}
