<?php

namespace WA\Repositories;

/**
 * Interface JobStatusRepositoryInterface.
 */
interface JobStatusRepositoryInterface
{
    /**
     * Get the first instance of the jobstatus.
     *
     * @param $name
     *
     * @return \WA\DataStore\JobStatus
     */
    public function findByName($name);

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getByName($name);

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getIdByName($name);
}
