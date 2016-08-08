<?php

namespace WA\Services\ApiHandler\ElasticSearch;

use Illuminate\Support\Facades\Input;
use WA\Services\ApiHandler\ApiHandlerInterface;

/**
 * Inspired largely by [Laravel Api Handler](https://github.com/marcelgwerder/laravel-api-handler).
 *
 * Class SQLApiHandler
 */
class ApiHandler implements ApiHandlerInterface
{
    /**
     * Return a new Result object for a single data set.
     *
     * @param mixed      $queryBuilder   Some kind of query builder instance
     * @param array|int  $identification Identification of the data set to work with
     * @param array|bool $queryParams    The parameters used for parsing
     *
     * @return \WA\Services\ApiHandler\SQL\Result Result object that provides getter methods
     */
    public function parseSingle($queryBuilder, $identification, $queryParams = false)
    {
        if ($queryParams === false) {
            $queryParams = Input::get();
        }

        $parser = new Parser($queryBuilder, $queryParams);
        $parser->parse($identification);

        return new Result($parser);
    }

    /**
     * Return a new Result object for multiple data sets.
     *
     * @param mixed      $queryBuilder          Some kind of query builder instance
     * @param array      $fullTextSearchColumns Columns to search in fulltext search
     * @param array|bool $queryParams           A list of query parameter
     *
     * @return \WA\Services\ApiHandler\SQL\Result Result object that provides getter methods
     */
    public function parseMultiple($queryBuilder, $fullTextSearchColumns = array(), $queryParams = false)
    {
        if ($queryParams === false) {
            $queryParams = Input::get();
        }

        $parser = new Parser($queryBuilder, $queryParams);
        $parser->parse($fullTextSearchColumns, true);

        return new Result($parser);
    }
}
