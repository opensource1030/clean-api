<?php

namespace WA\Repositories\GlobalSetting;

use WA\Repositories\RepositoryInterface;

/**
 * Interface UdlInterface.
 */
interface GlobalSettingInterface extends RepositoryInterface
{
    /**
     * Update a UDL.
     *
     * @param $data
     *
     * @return bool
     */
    public function update(array $data);

    /**
     * Create UDL.
     *
     * @param $data
     *
     * @return bool
     */
    public function create(array $data);
}
