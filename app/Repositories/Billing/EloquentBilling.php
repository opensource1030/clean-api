<?php

namespace WA\Repositories\Billing;

use Illuminate\Database\Eloquent\Model;
use WA\Repositories\AbstractRepository;
use WA\Repositories\Location\LocationInterface;
use WA\Services\Converter\Currency;

class EloquentBilling extends AbstractRepository implements BillingInterface
{
    /**
     * @var \WA\Repositories\Location\LocationInterface
     */
    protected $location;

    /**
     * @var Currency
     */
    protected $currency;

    protected $defaultCurrency = 'USD';

    public function __construct(Model $model, LocationInterface $location, Currency $currency)
    {
        $this->model = $model;
        $this->location = $location;
        $this->currency = $currency;

        $redis = app()['redis']->connection();
        $this->defaultCurrency = $redis->get('currency') ?: $this->defaultCurrency;
    }

    /**
     * Get the transformer on this system.
     *
     * @param $type  string
     *
     * @return mixed
     */
    public function getTransformerByType($type)
    {
        $type = ucfirst($type).'BillingTransformer';
        $class = '\WA\\DataStore\\Billing\\'.$type;

        if (!class_exists($class)) {
            throw new \InvalidArgumentException('Invalid Class');
        }

        return new $class();
    }

    /**
     * Get the total of a particular field.
     *
     * @param $column
     * @param  $type
     * @param $billMonth
     *
     * @return Money $total
     */
    public function getTotalOf($column, $type, $billMonth)
    {
        $column = $column.'TotalCharge';

        $locations = $this->getLocations();

        $runningTotal = 0;

        foreach ($locations as $location) {
            $t = $this->model
                ->select($column)
                ->where('locationId', $location['locationId'])
                ->where('billMonth', $billMonth)
                ->where('type', $type)
                ->sum($column);

            $convertTo = $this->location->byId($location['locationId']);
            $converted = $this->currency->convert($t, $convertTo->currencyIso, $this->defaultCurrency);

            $runningTotal += $converted;
        }

        return $runningTotal;
    }

    protected function getLocations()
    {
        $locations =
            $this->model
                ->groupBy('locationId')
                ->get(['locationId'])
                ->toArray();

        return $locations;
    }

    /**
     * Fot a set company, get the latest billMonth.
     *
     * @param $companyId
     *
     * @return string last billMonth Date
     */
    public function getLastBillMonth($companyId)
    {
        //@todo: get the proper bill month, per the relationships
        return '2015-04-01';
    }

    /**
     * Retrieve the filters for the Model.
     *
     * @param int  $companyId
     *
     * @return Array
     */
    public function addFilterToTheRequest($companyId) {
        //$aux['companies.id'] = (string) $companyId;
        return ''; //$aux;
    }

    /**
     * Check if the Model and/or its relationships are related to the Company of the User.
     *
     * @param JSON  $json : The Json request.
     * @param int  $companyId
     *
     * @return Boolean
     */
    public function checkModelAndRelationships($json, $companyId) {
        return true;
    }

    /**
     * Add the attributes or the relationships needed.
     *
     * @param $data : The Data request.
     *
     * @return $data: The Data with the minimum relationship needed.
     */
    public function addRelationships($data) {
        return $data;
    }
}
