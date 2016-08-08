<?php

namespace WA\Repositories\DeviceType;

use WA\Repositories\RepositoryInterface;

interface DeviceTypeInterface extends RepositoryInterface
{
    /**
     * Get the Device Type by is model name.
     *
     * @param string $name
     *
     * @return Object object of device type
     */
    public function byModel($name);

    /**
     * Get the Device Type by is model name or create if it doesn't exist.
     *
     * @param string $name
     * @param array  $data
     *
     * @return Object object of device type
     */
    public function byModelOrCreate($name, array $data);
}
