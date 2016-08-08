<?php

namespace WA\Services\ApiHandler\SQL;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use WA\Services\ApiHandler\ResultInterface;

class Result implements ResultInterface
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @param $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Return the query builder including the results.
     *
     * @return \Illuminate\Database\Query\Builder $result
     */
    public function getResult()
    {
        $paginate = (bool) $this->parser->getParam('paginate');
        $perPage = intval($this->parser->getParam('perPage'));

        $builder = $this->parser->getBuilder();

        // adding pagination
        if ($this->parser->getMultiple()) {
            if ($paginate) {
                $page = isset($_GET['_page']) ? $_GET['_page'] : 1;
                $pageName = isset($_GET['_page']) ? '_page' : 'page';

                $path = Request::url();
                $total = $builder->count();
                $results = $builder->forPage($page, $perPage)->get();

                $additionalQueries = array_diff_key($_GET, array_flip(['_page']));

                $pagedResponse = new LengthAwarePaginator($results, $total, $perPage, $page,
                    [
                        'pageName' => $pageName,
                        'query' => $additionalQueries,
                        'path' => $path,
                    ]);

                return $pagedResponse;
            } else {
                $result = $this->parser->getBuilder()->get();
            }
        } else {
            $result = $this->parser->getBuilder()->first();
        }

        return $result;
    }

    /**
     * Get the query builder object.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getBuilder()
    {
        return $this->parser->getBuilder();
    }

    /**
     * Get the mode of the parser.
     *
     * @return string
     */
    public function getMode()
    {
        return $this->parser->getMode();
    }
}
