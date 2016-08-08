<?php

namespace WA\Repositories\Location;

use WA\Repositories\AbstractRepository;

class EloquentLocation extends AbstractRepository implements LocationInterface
{
    /**
     * Get the available languages.
     *
     * @return array
     */
    public function getUniqueLang()
    {
        return $this->model
            ->whereNotNull('lang')
            ->groupBy('lang')
            ->get()->toArray();
    }

    /**
     * Get all the available countries.
     *
     * @return array of countries and related properties
     */
    public function getCountries()
    {
        return $this->model
            ->whereNotNull('name')
            ->groupBy('name')
            ->get()->toArray();
    }

    /**
     * Get all supported currencies ISO names (with optional rates).
     *
     * @param bool $rate the current rate
     */
    public function getCurrencies($rate = true)
    {
        $model = $this->model
            ->whereNotNull('currencyIso')
            ->where('exchangeRate', '<>', 0)
            ->groupBy('currencyIso');

        if (!$rate) {
            return $model->get(['currencyIso'])->toArray();
        }

        return $model->get(['currencyIso', 'exchangeRate'])->toArray();
    }

    /**
     * Get the currency symbol for a location.
     *
     * @param $currencyIso
     *
     * @return string currency symbol
     */
    public function getCurrencySymbol($currencyIso)
    {
        $symbol = $this->model->where('currencyIso', $currencyIso)->pluck('currencySymbol');

        return $symbol;
    }

    /**
     * Get the supported location Id.
     *
     * @return array of available location
     */
    public function getLocations()
    {
        return $this->model
            ->where('currencyIso', '<>', '')
            ->get(['id', 'name', 'region', 'currencySymbol', 'currencyIso', 'exchangeRate'])
            ->toArray();
    }

    /**
     * Get location details by name
     *
     * @param $name
     *
     * @return mixed
     */
    public function byName($name)
    {
        return $this->model->where('name', $name)->first();
    }
}
