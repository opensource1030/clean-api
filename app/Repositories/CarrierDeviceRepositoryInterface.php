<?php

namespace WA\Repositories;

/**
 * Interface CarrierDeviceRepositoryInterface.
 */
interface CarrierDeviceRepositoryInterface
{
    public function getPending();

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getNextPending($id);
}
