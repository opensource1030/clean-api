<?php

namespace WA\Services\ApiHandler;

interface ApiHandlerInterface
{
    /**
     * Return a new Result object for a single dataset.
     *
     * @param mixed      $queryBuilder   Some kind of query builder instance
     * @param array|int  $identification Identification of the dataset to work with
     * @param array|bool $queryParams    The parameters used for parsing
     *
     * @return Marcelgwerder\ApiHandler\Result Result object that provides getter methods
     */
    public function parseSingle($queryBuilder, $identification, $queryParams = false);

    /**
     * Return a new Result object for multiple datasets.
     *
     * @param mixed      $queryBuilder          Some kind of query builder instance
     * @param array      $fullTextSearchColumns Columns to search in fulltext search
     * @param array|bool $queryParams           A list of query parameter
     *
     * @return Result
     */
    public function parseMultiple($queryBuilder, $fullTextSearchColumns = array(), $queryParams = false);
}
