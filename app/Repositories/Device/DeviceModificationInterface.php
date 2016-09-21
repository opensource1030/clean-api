<?php

namespace WA\Repositories\Device;

use WA\Repositories\CountableInterface;
use WA\Repositories\RepositoryInterface;

interface DeviceModificationInterface extends RepositoryInterface
{
	/**
     * Get the API transformer used on this data store.
     *
     * @return mixed
     */
    public function getTransformer();
}
