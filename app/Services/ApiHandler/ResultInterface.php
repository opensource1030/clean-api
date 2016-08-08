<?php

namespace WA\Services\ApiHandler;

interface ResultInterface
{
    /**
     * Return the query builder including the results.
     *
     * @return \Illuminate\Database\Query\Builder $result
     */
    public function getResult();

    /**
     * Get the query builder object.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getBuilder();

    /**
     * Get the mode of the parser.
     *
     * @return string
     */
    public function getMode();
}
