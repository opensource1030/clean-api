<?php

namespace WA\Http\Controllers;

use Dingo\Api\Routing\Helpers;
use Input;
use WA\Http\Requests\Parameters\Sorting;


/**
 * Class ApiController.
 */
abstract class ApiController extends BaseController
{
    use Helpers;

    protected $filters = null;
    protected $sort = null;

    public function getSortAndFilters() {
        $this->getFilters();
        $this->getSort();
    }

    public function getFilters()
    {
        $this->filters = (array)\Request::get('filter', null);
        return $this->filters;
    }

    public function getSort()
    {
        if (\Request::get('sort')) {
            $sort = \Request::get('sort');
            $sorting = new Sorting();
            if (!empty($sort) && is_string($sort)) {
                $members = \explode(',', $sort);
                if (!empty($members)) {
                    foreach ($members as $field) {
                        $key = ltrim($field, '-');
                        $sorting->addField($key, ('-' === $field[0]) ? 'desc' : 'asc');
                    }
                }
            }
            $this->sort = $sorting;
            return $this->sort;
        }
        return null;
    }
}
