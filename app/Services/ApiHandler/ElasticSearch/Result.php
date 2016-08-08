<?php

namespace WA\Services\ApiHandler\ElasticSearch;

use Illuminate\Support\Facades\Request;
use WA\Services\ApiHandler\ResultInterface;

class Result implements ResultInterface
{
    protected $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Return the query builder including the results.
     *
     * @return Illuminate\Database\Query\Builder $result
     */
    public function getResult()
    {
        $paginate = (bool) $this->parser->getParam('paginate');
        $perPage = intval($this->parser->getParam('perPage'));

        $result = $this->parser->getParsedCollection();

        $path = Request::url();
        $total = $result->getHits();

        $additionalQueries = array_diff_key($_GET, array_flip(['_page']));
    }

    /**
     * Get the query builder object.
     *
     * @return Illuminate\Database\Query\Builder
     */
    public function getBuilder()
    {
        // TODO: Implement getBuilder() method.
    }

    /**
     * Get the mode of the parser.
     *
     * @return string
     */
    public function getMode()
    {
        // TODO: Implement getMode() method.
    }
}
