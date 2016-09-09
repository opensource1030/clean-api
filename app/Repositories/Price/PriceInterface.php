<?php

namespace WA\Repositories\Price;

use WA\Repositories\RepositoryInterface;

/**
 * Interface PriceInterface
 *
 * @package WA\Repositories\Price
 */
interface PriceInterface extends RepositoryInterface
{
    /**
     * Get Array of all Prices.
     *
     * @return Array of Price
     */
    public function getAllPrice();

    /**
     * Create Price
     *
     * @param array $data
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

}