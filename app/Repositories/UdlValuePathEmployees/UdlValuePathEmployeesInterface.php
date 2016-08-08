<?php

namespace WA\Repositories\UdlValuePathEmployees;

use WA\Repositories\RepositoryInterface;

interface UdlValuePathEmployeesInterface extends RepositoryInterface
{
    /**
     * Get the ExternalId value that matches the UDLValuePathId.
     *
     * @param string $udlValuePathId
     *
     * @return Object object of the udlValuePathEmployees information
     */
    public function byUdlPathId($udlValuePathId);

    /**
     * Get the ExternalId value that matches the creator Id.
     *
     * @param int $creatorId
     *
     * @return Object object of the udlValuePathEmployees information
     */
    public function byCreatorId($creatorId);
}
