<?php


namespace WA\Repositories;

use WA\DataStore\Carrier\Carrier;

/**
 * Class CarrierRepository.
 */
class CarrierRepository extends BaseRepository implements CarrierRepositoryInterface
{
    protected $cache = 30;

    /**
     * @param Carrier $dataStore
     */
    public function __construct(Carrier $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    /**
     * @return array|static[]
     */
    public function getActive()
    {
        return $this->dataStore->whereActive(1)->orderBy('name')->get();
    }

    /**
     * Get the carrier by the name.
     *
     * @param $name
     *
     * @return mixed
     */
    public function getByName($name)
    {
        return $this->dataStore->whereActive(1)
            ->where('name', '=', $name)
            ->first();
    }

    /**
     * Get then carrier by the ID.
     *
     * @param $id
     *
     * @return mixed
     */
    public function getById($id)
    {
        return $this->dataStore->whereId($id)
            ->first();
    }
}
