<?php

namespace WA\Repositories;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface DeviceTypeRepositoryInterface.
 */
interface DeviceTypeRepositoryInterface
{
    /**
     * Get the unique fields (as default).
     *
     * @param field
     *
     * @return Collection| \WA\DataStore\DeviceType
     */
    public function getUniqueColumn($field);

    /**
     * Get the count of suspended/pending devices.
     */
    public function getPendingCount();
}
