<?php

namespace WA\Repositories\Location;

use WA\Repositories\RepositoryInterface;

interface LocationInterface extends RepositoryInterface
{
    /**
     * Get Array of all Images.
     *
     * @return array of Image
     */
    public function getAllLocation();

    /**
     * Get location details by name.
     *
     * @param $name
     *
     * @return mixed
     */
    public function byName($name);
}
