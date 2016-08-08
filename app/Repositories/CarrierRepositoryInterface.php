<?php


namespace WA\Repositories;

/**
 * Interface CarrierRepositoryInterface.
 */
interface CarrierRepositoryInterface
{
    public function getActive();

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getByName($name);

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getById($id);
}
