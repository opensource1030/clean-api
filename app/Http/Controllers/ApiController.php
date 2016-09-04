<?php

namespace WA\Http\Controllers;

use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use WA\Http\Requests\Parameters\Filters;
use WA\Http\Requests\Parameters\Sorting;


/**
 * Extensible API controller
 *
 * Class ApiController.
 */
abstract class ApiController extends BaseController
{
    use Helpers;

    /**
     * @var Filters
     */
    protected $filters = null;

    /**
     * @var Sorting
     */
    protected $sort = null;

    /**
     * @var array
     */
    protected $criteria = [];

    /**
     * Get sorting and filtering criteria from the request
     *
     * @return array
     */
    public function getRequestCriteria()
    {
        $this->filters = new Filters((array)\Request::get('filter', null));
        $this->sort = new Sorting(\Request::get('sort', null));
        
        $this->criteria['filters'] = $this->filters;
        $this->criteria['sort'] = $this->sort;
        return $this->criteria;
    }

    public function applyMeta(Response $response)
    {
        $response->addMeta('sort', $this->sort->get());
        $response->addMeta('filter', $this->filters->get());
        return $response;
    }
}
