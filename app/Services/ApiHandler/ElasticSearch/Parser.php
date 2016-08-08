<?php

namespace WA\Services\ApiHandler\ElasticSearch;

use Illuminate\Support\Facades\Config;
use WA\Services\ApiHandler\ParserInterface;

class Parser implements ParserInterface
{
    /**
     * The builder Instance.
     *
     * @var mixed
     */
    protected $builder;

    /**
     * The HTTP query params.
     *
     * @var array
     */
    protected $params;

    /**
     * The original builder Instance.
     *
     * @var mixed
     */
    protected $originalBuilder;

    /**
     * If the parser works on multiple datasets.
     *
     * @var bool
     */
    protected $multiple;

    /**
     * All fields that belong to a relation.
     *
     * @var array
     */
    protected $additionalFields = [];

    /**
     * Predefined functions.
     *
     * @var array
     */
    protected $functions = ['fields', 'sort', 'limit', 'offset', 'config', 'with', 'q'];

    /**
     * All sorts that belong to a relation.
     *
     * @var array
     */
    protected $additionalSorts = [];

    /**
     * Retrieved Eloquent Collection.
     *
     * @var \Elasticquent\ElasticquentResultCollection
     */
    protected $parsedCollection;

    /**
     * @param $builder
     * @param $params
     */
    public function __construct($builder, $params)
    {
        $this->builder = $builder;
        $this->params = $params;
        $this->prefix = Config::get('apihandler.prefix');

        $this->functions = array_merge($this->functions, Config::get('apihandler.additional_functions'));

        $this->isEloquentModel = is_subclass_of($builder, '\Illuminate\Database\Eloquent\Model');

        $canDoElasticSearch = ($builder->getElasticSearchClient() instanceof \Elasticsearch\Client);

        if ($this->isEloquentModel && $canDoElasticSearch) {

            //convert to builder object
            $this->builder = $builder->newQuery();
            $this->isEloquentModel = true;
        } else {
            throw new \InvalidArgumentException('Model cannot run an Elastic Search ');
        }

        $this->originalBuilder = clone $this->builder;
    }

    /**
     * Parse the query parameters with the given options.
     * Either for a single dataset or multiple.
     *
     * @param mixed $options
     * @param bool  $multiple
     */
    public function parse($options, $multiple = false)
    {
        $model = $this->builder->getModel();

        if (empty($model)) {
            throw new \InvalidArgumentException('This Model is Invalid');
        }

        if ($offset = $this->getParam('offset')) {
            $this->setEsQuery(['offset' => $offset]);
        }

        if ($sort = $this->getParam('sort')) {
            $this->setEsQuery(['sort' => $sort]);
        }

        if ($type = $this->getParam('type')) {
            $this->setEsQuery(['type' => $type]);
        }

        if ($limit = $this->getParam('perPage')) {
            $this->setEsQuery(['perPage' => $this->getParam('perPage')]);
        }

        //Parse and apply the filters built for the Elastic query
        //Every parameter that has not a predefined functionality gets parsed as a filter
        if ($filterParams = $this->getFilterParams()) {
            $this->parseFilter($filterParams);
        }

        if ($fields = $this->getParam('fields')) {
            $this->parseFields($fields);
        }

        //Parse and apply sort elements using the laravel "orderBy" function
        if ($sort = $this->getParam('sort')) {
            $this->parseSort($sort);
        }

        $currentQuery = $model->getElasticQuery($type);
        $this->parsedCollection = $model::searchByQuery($currentQuery['query'], $currentQuery['aggs'], null, $limit,
            $offset, null);
    }

    /**
     * Get the currently passed in config option.
     *
     * @param $config
     *
     * @return mixed
     */
    public function getConfig($config)
    {
        return Config::get($config);
    }

    /**
     * Get the builder instance.
     *
     * @return mixed
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * Get the returned multiple setting.
     *
     * @return mixed
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * Get a parameter.
     *
     * @param $param
     *
     * @return string|bool
     */
    public function getParam($param)
    {
        if (isset($this->params[$this->prefix.$param])) {
            return $this->params[$this->prefix.$param];
        }

        return false;
    }

    /**
     * Set the variables for Elastic Search Query.
     *
     * @param array $params
     */
    protected function setEsQuery(array $params)
    {
    }

    protected function getFilterParams()
    {
        $reserved = array_fill_keys($this->functions, true);
        $prefix = $this->prefix;

        $filterParams = array_diff_ukey($this->params, $reserved, function ($a, $b) use ($prefix) {
            return $a != $prefix.$b;
        });

        if (count($filterParams) > 0) {
            return $filterParams;
        }

        return false;
    }

    /**
     * Parse the filter.
     *
     * @param $filterParams
     */
    protected function parseFilter($filterParams)
    {
    }

    /**
     * Parse the fields parameter and return the proper fields.
     *
     * @param $fieldsParam
     *
     * @return array field
     */
    protected function parseFields($fieldsParam)
    {
        $fields = [];

        foreach (explode(',', $fieldsParam) as $field) {
            //Only add the fields that are on the base resource
            if (strpos($field, '.') === false) {
                $fields[] = trim($field);
            } else {
                $this->additionalFields[] = trim($field);
            }
        }

        //@todo: construct per ES
    }

    /**
     * Parse the sort param and determine whether the sorting is ascending or descending.
     * A descending sort has a leading "-". Apply it to the query.
     *
     * @param $sortParam
     */
    protected function parseSort($sortParam)
    {
        foreach (explode(',', $sortParam) as $sortElem) {
            //Check if ascending or descending(-) sort
            if (preg_match('/^-.+/', $sortElem)) {
                $direction = 'desc';
            } else {
                $direction = 'asc';
            }

            $pair = [preg_replace('/^-/', '', $sortElem), $direction];

            //@todo apply to ES Query
        }
    }

    /**
     * @return \Elasticquent\ElasticquentResultCollection
     */
    public function getParsedCollection()
    {
        return $this->parsedCollection;
    }
}
