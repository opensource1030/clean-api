<?php

namespace WA\Helpers\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;
use WA\DataStore\BaseDataStore;
use WA\Exceptions\BadCriteriaException;
use WA\Http\Requests\Parameters\Fields;
use WA\Http\Requests\Parameters\Filters;
use WA\Http\Requests\Parameters\Sorting;

trait Criteria
{
    /**
     * @var \Illuminate\Database\Eloquent\Model|BaseDataStore
     */
    protected $criteriaModel;

    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $criteriaQuery;

    /**
     * @var Sorting
     */
    protected $sortCriteria = null;

    /**
     * @var Filters
     */
    protected $filterCriteria = null;

    /**
     * @var array
     */
    protected $criteria = [
        'sort'    => [],
        'filters' => [],
        'fields'  => []
    ];

    /**
     * @var Filters
     */
    protected $filters = null;

    /**
     * @var Sorting
     */
    protected $sort = null;

    /**
     * @var Fields
     */
    protected $fields = null;

    public $criteriaModelName = null;
    protected $criteriaModelColumns = null;

    /**
     * CriteriaTransformer constructor.
     *
     * @param array $criteria
     */
    public function __construct($criteria = [])
    {
        $this->criteria = $criteria;
    }

    /**
     * Get a query-builder instance for this model.
     *
     * @param $criteriaModel
     * @param bool $clear
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getQuery($criteriaModel, $clear = false)
    {
        if ($clear == true) {
            $this->criteriaQuery = null;
        }
        if ($this->criteriaQuery === null) {
            if ($criteriaModel instanceof Relation) {
                $this->criteriaQuery = $criteriaModel;
                $this->criteriaModelName = $criteriaModel->getRelated()->getTable();
                $this->criteriaModelColumns = $criteriaModel->getRelated()->getTableColumns();
            } elseif ($criteriaModel instanceof BaseDataStore) {
                $this->criteriaQuery = $criteriaModel->newQuery();
                $this->criteriaModelName = $criteriaModel->getTable();
                $this->criteriaModelColumns = $criteriaModel->getTableColumns();
            }
        }

        return $this->criteriaQuery;
    }

    /**
     * Convenience method to set all criteria at once.
     *
     * @param array $criteria
     *
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
     * Set sort criteria.
     *
     * @param Sorting $sortCriteria
     *
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
     * Set filter criteria.
     *
     * @param Filters $filterCriteria
     *
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
     * @param $criteriaModel
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyCriteria($criteriaModel, $criteria = null)
    {
        if ($criteria !== null) {
            $this->setCriteria($criteria);
        }

        $this->criteriaModel = $criteriaModel;

        $this->getQuery($criteriaModel, true);
        $this->sort()->filter();

        return $this->criteriaQuery;
    }

    /**
     * Apply filter criteria to the current query.
     *
     * @return $this
     *
     * @throws BadCriteriaException
     */
    protected function filter()
    {
        if ($this->filterCriteria === null) {
            return $this;
        }

        $criteriaModelName = $this->criteriaModelName;
        $criteriaModelColumns = $this->criteriaModelColumns;

        foreach ($this->filterCriteria->filtering() as $filterKey => $filterVal) {
            if (strpos($filterKey, '.')) {
                if (substr($filterKey, 0, strpos($filterKey, '.')) !== $criteriaModelName) {
                    continue;
                }
                $filterKey = substr($filterKey, strpos($filterKey, '.') + 1);
            }

            if (in_array($filterKey, $criteriaModelColumns)) {
                $op = strtolower(key($filterVal));
                $val = current($filterVal);
                switch ($op) {
                    case 'gt':
                        $this->criteriaQuery->where($filterKey, '>', $val);
                        break;
                    case 'lt':
                        $this->criteriaQuery->where($filterKey, '<', $val);
                        break;
                    case 'ge':
                        $this->criteriaQuery->where($filterKey, '>=', $val);
                        break;
                    case 'le':
                        $this->criteriaQuery->where($filterKey, '>=', $val);
                        break;
                    case 'ne':
                        // Handle delimited lists
                        $vals = explode(',', $val);
                        $vals = $this->extractAdvancedCriteria($vals);
                        if (count($vals) === 0) {
                            continue;
                        }
                        $this->criteriaQuery->whereNotIn($filterKey, $vals);
                        break;
                    case 'eq':
                        // Handle delimited lists
                        $vals = explode(',', $val);
                        $vals = $this->extractAdvancedCriteria($vals);
                        if (count($vals) === 0) {
                            continue;
                        }
                        $this->criteriaQuery->whereIn($filterKey, $vals);
                        break;
                    case 'like':
                        $val = str_replace('*', '%', $val);
                        $this->criteriaQuery->where($filterKey, 'LIKE', $val);
                        break;
                    default:
                        throw new BadCriteriaException('Invalid filter operator');
                        break;
                }
            } else {
                throw new BadCriteriaException('Invalid filter criteria');
            }
        }

        return $this;
    }

    /**
     * Apply sort criteria to the current query.
     *
     * @return $this
     *
     * @throws BadCriteriaException
     */
    protected function sort()
    {
        if ($this->sortCriteria === null) {
            return $this;
        }

        foreach ($this->sortCriteria->sorting() as $sortColumn => $direction) {
            if (in_array($sortColumn, $this->criteriaModel->getTableColumns())) {
                $this->criteriaQuery->orderBy($sortColumn, $direction);
            } else {
                throw new BadCriteriaException('Invalid sort criteria');
            }
        }

        return $this;
    }

    /**
     * @param $vals
     *
     * @return mixed
     */
    protected function extractAdvancedCriteria($vals)
    {
        // Ignore more complicated criteria, it's handled elsewhere
        foreach ($vals as $key => $val) {
            if (strpos($val, '[') === 0) {
                unset($vals[$key]);
            }
        }

        return $vals;
    }

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
}
