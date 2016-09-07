<?php

namespace WA\Repositories\Device;

use WA\Repositories\CountableInterface;
use WA\Repositories\RepositoryInterface;

interface DevicePriceInterface extends RepositoryInterface
{
    /**
     * Get the API transformer used on this data store.
     *
     * @return mixed
     */
    public function getTransformer();

    /**
     * Update a repository.
     *
     * @param array $data to be updated
     *
     * @return Object object of updated repo
     */
    public function update(array $data);
}
