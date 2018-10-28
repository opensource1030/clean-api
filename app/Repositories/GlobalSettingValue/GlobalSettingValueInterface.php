<?php

namespace WA\Repositories\UdlValue;

use WA\Repositories\RepositoryInterface;

/**
 * Interface UdlValueInterface.
 */
interface GlobalSettingValueInterface extends RepositoryInterface
{
    /**
     * Creates a new UDL value.
     *
     * @param array $data
     *
     * @return bool
     */
    public function create(array $data);

    /**
     * Update a UDL value.
     *
     * @param $data
     *
     * @return bool
     */
    public function update(array $data);

}
