<?php

namespace WA\Http\Controllers;

use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use WA\Http\Requests\Parameters\Fields;
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
    protected $criteria = [
        'sort'    => [],
        'filters' => [],
        'fields'  => []
    ];

    /**
     * @return mixed
     */
    public function getRequestCriteria()
    {
        $filters = $this->getFilters();
        $sort = $this->getSort();
        $fields = $this->getFields();

        $this->criteria['filters'] = $filters;
        $this->criteria['sort'] = $sort;
        $this->criteria['fields'] = $fields;
        return $this->criteria;
    }

    /**
     * @return Sorting
     */
    public function getSort()
    {
        $sort = new Sorting(\Request::get('sort', null));
        return $sort;
    }

    /**
     * @return Filters
     */
    public function getFilters()
    {
        $filters = new Filters((array)\Request::get('filter', null));
        return $filters;
    }


    /**
     * @return Fields
     */
    public function getFields()
    {
        $fields = new Fields(\Request::get('fields', null));
        return $fields;
    }

    public function applyMeta(Response $response)
    {
        $response->addMeta('sort', $this->criteria['sort']->get());
        $response->addMeta('filter', $this->criteria['filters']->get());
        $response->addMeta('fields', $this->criteria['fields']->get());
        return $response;
    }
}
