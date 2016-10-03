<?php

namespace WA\DataStore;

use League\Fractal\TransformerAbstract;
use WA\Exceptions\BadCriteriaException;
use WA\Http\Requests\Parameters\Filters;
use WA\Http\Requests\Parameters\Sorting;

abstract class BaseTransformer extends TransformerAbstract
{

    /**
     * @var \Illuminate\Database\Eloquent\Model|BaseDataStore
     */
    protected $model;

    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    /**
     * @var Sorting
     */
    protected $sortCriteria = null;

    /**
     * @var Filters
     */
    protected $filterCriteria = null;

    /**
     * Get a query-builder instance for this model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getQuery($model, $clear = false)
    {
        if ($clear == true || $this->query === null) {
            $this->query = $model;
        }
        return $this->query;
    }

    /**
     * Convenience method to set all criteria at once
     *
     * @param array $criteria
     * @return bool
     */
    public function setCriteria($criteria = [])
    {
        if (isset($criteria['sort'])) {
            $this->setSort($criteria['sort']);
        }

        if (isset($criteria['filters'])) {
            $this->setFilters($criteria['filters']);
        }

        return true;
    }

    /**
     * Set sort criteria
     *
     * @param Sorting $sortCriteria
     * @return $this
     */
    public function setSort(Sorting $sortCriteria)
    {
        if ($sortCriteria !== null) {
            $this->sortCriteria = $sortCriteria;
        }
        return $this;
    }

    /**
     * Set filter criteria
     *
     * @param Filters $filterCriteria
     * @return $this
     */
    public function setFilters(Filters $filterCriteria)
    {
        if ($filterCriteria !== null) {
            $this->filterCriteria = $filterCriteria;
        }
        return $this;
    }

    /**
     * @param $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyCriteria($model, $criteria)
    {
        $this->setCriteria($criteria);
        $this->model = $model;
        $this->getQuery($model, true);
        $this->sort()->filter();
        return $this->query;
    }

    /**
     * Apply filter criteria to the current query
     *
     * @return $this
     * @throws BadCriteriaException
     */
    protected function filter()
    {
        if ($this->filterCriteria === null) {
            return $this;
        }

        $modelName = $this->model->getRelated()->getTable();
        $modelColumns = $this->model->getRelated()->getTableColumns();


        foreach ($this->filterCriteria->filtering() as $filterKey => $filterVal) {
            if (strpos($filterKey, ".")) {
                if (substr($filterKey, 0, strpos($filterKey, ".")) !== $modelName) {
                    continue;
                }
                $filterKey = substr($filterKey, strpos($filterKey, ".") + 1);
            }

            if (in_array($filterKey, $modelColumns)) {
                $op = strtolower(key($filterVal));
                $val = current($filterVal);
                switch ($op) {
                    case "gt":
                        $this->query->where($filterKey, '>', $val);
                        break;
                    case "lt":
                        $this->query->where($filterKey, '<', $val);
                        break;
                    case "ge":
                        $this->query->where($filterKey, '>=', $val);
                        break;
                    case "le":
                        $this->query->where($filterKey, '>=', $val);
                        break;
                    case "ne":
                        // Handle delimited lists
                        $vals = explode(",", $val);
                        $this->query->whereNotIn($filterKey, $vals);
                        break;
                    case "eq":
                        // Handle delimited lists
                        $vals = explode(",", $val);
                        $this->query->whereIn($filterKey, $vals);
                        break;
                    case "like":
                        $val = str_replace("*", "%", $val);
                        $this->query->where($filterKey, 'LIKE', $val);
                        break;
                    default:
                        throw new BadCriteriaException("Invalid filter operator");
                        break;
                }
            } else {
                throw new BadCriteriaException("Invalid filter criteria");
            }
        }
        return $this;
    }

    /**
     * Apply sort criteria to the current query
     *
     * @return $this
     * @throws BadCriteriaException
     */
    protected function sort()
    {

        if ($this->sortCriteria === null) {
            return $this;
        }

        foreach ($this->sortCriteria->sorting() as $sortColumn => $direction) {
            if (in_array($sortColumn, $this->model->getTableColumns())) {
                $this->query->orderBy($sortColumn, $direction);
            } else {
                throw new BadCriteriaException("Invalid sort criteria");
            }
        }

        return $this;
    }
}
