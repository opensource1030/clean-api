<?php


namespace WA\Repositories;

/**
 * Interface AssetRepositoryInterface.
 */
interface AssetRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find of create a non-existing asset and assign it to a user (if is doesn't exist).
     *
     * @param string $identification
     */
    public function matchIdentificationToUser($identification);
}
