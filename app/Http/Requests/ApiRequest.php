<?php

namespace WA\Http\Requests;

use Dingo\Api\Http\Request as BaseRequest;
use WA\Http\Requests\Parameters\Fields;
use WA\Http\Requests\Parameters\Filters;
use WA\Http\Requests\Parameters\Sorting;


/**
 * Class Api Request
 */
class ApiRequest extends BaseRequest
{
    /**
     * Get sorting and filtering criteria from the request
     *
     * @return array
     */
    public function getRequestCriteria()
    {
        $filters = $this->getFilters();
        $sort = $this->getSort();
        $fields = $this->getFields();

        $criteria['filters'] = $filters;
        $criteria['sort'] = $sort;
        $criteria['fields'] = $fields;
        return $criteria;
    }

    /**
     * @return Sorting
     */
    public function getSort()
    {
        $sort = new Sorting($this->get('sort', null));
        return $sort;
    }

    /**
     * @return Filters
     */
    public function getFilters()
    {
        $filters = new Filters((array)$this->get('filter', null));
        return $filters;
    }


    /**
     * @return Fields
     */
    public function getFields()
    {
        $fields = new Fields($this->get('fields', null));
        return $fields;
    }
}
