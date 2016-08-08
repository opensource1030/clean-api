<?php

namespace WA\Services\Converter;

use WA\Repositories\Location\LocationInterface;

/**
 * Converts currencies, using US as the home base currency.
 *
 *Just be aware that google and yahoo aren't free for commercial use. The formula to convert is very simple.
 * By definition you are always storing rates against a base currency.
 * So lets say you have Currency1, Currency2 & CurrencyBase.
 * You will have stored the rate of Currency1/CurrencyBase as Cur1Rate and the rate Currency2/CurrencyBase as Cur2Rate.
 * To get Currency1/Currency2 rate Cur1Rate/Cur2Rate. You can do this dynamically as required.
 *
 * Class Converter
 */
class Currency
{
    protected $location;

    protected $supportedCurrency = [];

    protected $conversionChart = [];

    protected $baseCurrency = 'USD';

    /**
     * @param LocationInterface $location
     */
    public function __construct(LocationInterface $location)
    {
        $this->setSupportedCurrency($location->getCurrencies(false));
        $this->setConversionChart($this->conversionChart = $location->getCurrencies());
        $this->location = $location;
    }

    /**
     * TODO: cache this
     * Set the conversion chart used by the table.
     *
     * @param array $chartBucket
     */
    public function setConversionChart($chartBucket = [])
    {
        $chart = [];
        if (!is_null($chartBucket) && $this->array_key_exists_r('currencyIso', $chartBucket)) {
            foreach ($chartBucket as $c) {
                $chart[ $c[ 'currencyIso' ] ] = $c[ 'exchangeRate' ];
            }
        } else {
            $chart = $chartBucket;
        }

        $this->conversionChart = $chart;
    }

    /**
     * TODO: cache this
     * Set the support currency.
     *
     * @param array $currencyBucket
     */
    public function setSupportedCurrency($currencyBucket = [])
    {
        $currency = [];

        if (!is_null($currencyBucket) && $this->array_key_exists_r('currencyIso', $currencyBucket)) {
            foreach ($currencyBucket as $curr) {
                $currency[ ] = $curr[ 'currencyIso' ];
            }
        } else {
            $currency = $currencyBucket;
        }

        $this->supportedCurrency = $currency;
    }

    /**
     * Get the supported currency.
     *
     * @return array
     */
    public function getSupportedCurrency()
    {
        return $this->supportedCurrency;
    }

    /**
     * Get the supported currency.
     *
     * @return array
     */
    public function getConversionChart()
    {
        return $this->conversionChart;
    }

    /**
     * Converts a currency from a base to another
     * we assume the US as the base currency.
     *
     * @param $value
     * @param $from
     * @param $to
     *
     * @return float of converted value
     */
    public function convert($value, $from, $to)
    {
        if (!$this->isValidCurrency($from) || !$this->isValidCurrency($to)) {
            $rate = 1;
        } else {
            $rate = $this->getExchangeRate($from, $to);
        }

        return $converted = round($value * $rate, 2);
    }

    /**
     * @param $currency
     *
     * @return bool
     */
    protected function isValidCurrency($currency)
    {
        return in_array($currency, $this->supportedCurrency);
    }

    /**
     * @param $needle
     * @param $haystack
     *
     * @return bool
     */
    private function array_key_exists_r($needle, $haystack)
    {
        $result = array_key_exists($needle, $haystack);
        if ($result) {
            return $result;
        }
        foreach ($haystack as $v) {
            if (is_array($v)) {
                $result = $this->array_key_exists_r($needle, $v);
            }
            if ($result) {
                return $result;
            }
        }

        return $result;
    }

    /**
     * @param $currencyFrom
     * @param $currencyTo
     *
     * @return float
     */
    protected function getExchangeRate($currencyFrom, $currencyTo)
    {
        $currencyFromRate = $this->conversionChart[ $currencyFrom ];
        $currencyToRate = $this->conversionChart[ $currencyTo ];

        return ($currencyToRate / $currencyFromRate);
    }
}
