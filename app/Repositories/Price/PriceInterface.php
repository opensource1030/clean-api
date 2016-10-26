<?php

namespace WA\Repositories\Price;

use WA\Repositories\RepositoryInterface;

/**
 * Interface PriceInterface.
 */
interface PriceInterface extends RepositoryInterface
{
    /**
     * Get Array of all Prices.
     *
     * @return array of Price
     */
    public function getAllPrice();

    /**
     * Create Price.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data);

    /**
     * Update Price.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data);

    /**
     * Delete Price.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true);

    /**
     * Get Array of all Prices Devices.
     *
     * @return array of Prices
     */
    public function getPriceDevices($id);

    /**
     * Get Array of all Prices Capacities.
     *
     * @return array of Prices
     */
    public function getPriceCapacities($id);

    /**
     * Get Array of all Prices Styles.
     *
     * @return array of Prices
     */
    public function getPriceStyles($id);

    /**
     * Get Array of all Prices Carriers.
     *
     * @return array of Prices
     */
    public function getPriceCarriers($id);

    /**
     * Get Array of all Prices Companies.
     *
     * @return array of Prices
     */
    public function getPriceCompanies($id);
}
