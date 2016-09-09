<?php

namespace WA\Repositories;

use Illuminate\Database\Eloquent\Model;
use Log;
use WA\DataStore\BaseDataStore;
use WA\Exceptions\BadCriteriaException;
use WA\Http\Requests\Parameters\Filters;
use WA\Http\Requests\Parameters\Sorting;

abstract class AbstractRepository implements RepositoryInterface
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
     * AbstractRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get a query-builder instance for this model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getQuery()
    {
        if ($this->query === null) {
            $model = $this->model;
            $this->query = $model::query();
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
     * Convenience method to apply sorting and filtering criteria
     *
     * @return $this
     */
    protected function applyCriteria()
    {
        return $this->sort()->filter();
    }

    /**
     * Apply filter criteria to the current query
     *
     * @return $this
     * @throws BadCriteriaException
     */
    protected function filter()
    {
        $this->getQuery();

        if ($this->filterCriteria === null) {
            return $this;
        }

        foreach ($this->filterCriteria->filtering() as $filterKey => $filterVal) {
            if (in_array($filterKey, $this->model->getTableColumns())) {
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
        $this->getQuery();

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


    /**
     * Get paginated resource
     *
     * @param int $perPage
     * @param bool $api false|true
     * @param bool $paginate
     *
     * @return mixed Object as Collection of object information, | Paginator Collection if pagination is true (default)
     */
    public function byPage($paginate = true, $perPage = 25, $api = false)
    {
        // Apply filtering and sorting criteria, if set
        $this->applyCriteria();

        if (!$paginate) {
            return $this->query->get();
        }

        return $this->query->paginate($perPage);
    }

    /**
     * Wrapper function.
     *
     * @param $id
     *
     * @return Object
     */
    public function getById($id)
    {
        return $this->byId($id);
    }

    /**
     * Get the model by its Id.
     *
     * $param int $id
     *
     * @return Object object of model information
     */
    public function byId($id)
    {
        if (is_array($id)) {
            return $this->model->whereIn('id', $id)
                ->get();
        }

        return $this->model->findOrFail($id);
    }

    /**
     * Create a repository.
     *
     * @param array $data to be created
     *
     * @return mixed Object object of created model
     */
    public function create(array $data)
    {
        try {
            return $this->model->create($data);
        } catch (\PDOException $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Get the model used on the class.
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Delete from the repo by the ID.
     *
     * @param int $id
     * @param bool $force completely remove for the DB instead of marking it as "deleted"
     * @return int
     */
    public function deleteById($id, $force = false)
    {
        if ($force && !is_array($id)) {
            $instance = $this->byId($id);
            $instance->forceDelete();
        }

        return $this->model->destroy($id);
    }
}
