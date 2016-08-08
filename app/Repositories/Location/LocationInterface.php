<?php

namespace WA\Repositories\Location;

use WA\Repositories\RepositoryInterface;

interface LocationInterface extends RepositoryInterface
{
    /**
     * Get the available languages.
     *
     * @return mixed
     */
    public function getUniqueLang();

    /**
     * Get all the available countries.
     */
    public function getCountries();

    /**
     * Get all supported currencies.
     *
     * @param bool $rate the current rate
     */
    public function getCurrencies($rate = true);

    /**
     * Get the currency symbol for a location.
     *
     * @param $currencyIso
     *
     * @return string currency symbol
     */
    public function getCurrencySymbol($currencyIso);

    /**
     * Get the supported location Id.
     *
     * @return array of available location
     */
    public function getLocations();

    /**
     * Get location details by name
     *
     * @param $name
     *
     * @return mixed
     */
    public function byName($name);
}
