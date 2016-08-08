<?php

namespace WA\Repositories;

use WA\DataStore\JobStatus;
use Cache;

/**
 * Class JobStatusRepository.
 */
class JobStatusRepository extends BaseRepository implements JobStatusRepositoryInterface
{
    /**
     * @param JobStatus $dataStore
     */
    public function __construct(JobStatus $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    /**
     * Get the first instance of the jobstatus.
     *
     * @param $name
     *
     * @return \WA\DataStore\JobStatus
     */
    public function findByName($name)
    {
        $query = $this->dataStore->whereName($name);

        $value = Cache::remember('statusFindByName'.$name, $this->cacheFor, function () use ($query) {
            return $query->first();
        });

        return $value;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function getIdByName($name)
    {
        $status = $this->getByName($name);
        if (isset($status)) {
            return $status->id;
        }

        return false;
    }

    /**
     * @param $name
     *
     * @return mixed|static
     */
    public function getByName($name)
    {
        $query = $this->dataStore->whereName($name);

        $value = Cache::remember('statusGetByName'.$name, $this->cacheFor, function () use ($query) {
            return $query->first();
        });

        return $value;
    }
}
