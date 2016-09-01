<?php

namespace WA\Repositories;

use Illuminate\Database\Eloquent\Model;
use Log;
use Paginator;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var
     */
    protected $query;

    protected $sortCriteria = null;

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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getQuery()
    {
        if ($this->query === null) {
            $model = $this->model;
            $this->query = $model::query();
        }
        return $this->query;
    }

    /**
     * @param $sortCriteria
     * @return $this
     */
    public function setSort($sortCriteria)
    {
        if ($sortCriteria !== null) {
            $this->sortCriteria = $sortCriteria;
        }
        return $this;
    }

    /**
     * @param $filterCriteria
     * @return $this
     */
    public function setFilters($filterCriteria)
    {
        if ($filterCriteria !== null) {
            $this->filterCriteria = $filterCriteria;
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function filter()
    {
        $this->getQuery();

        if (!$this->filterCriteria === null) {
            return $this;
        }

        foreach ($this->filterCriteria as $filterKey => $filterVal) {
            if (!is_array($filterVal)) {
                $this->query->where($filterKey, '=', $filterVal);
            } else {
                // not yet implemented
            }
        }
        return $this;
    }

    protected function sort()
    {
        $this->getQuery();

        if ($this->sortCriteria === null) {
            return $this;
        }

        foreach ($this->sortCriteria->sorting() as $sort => $direction) {
            $this->query->orderBy($sort, $direction);
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
     * @return Object as Collection of object information, | Paginator Collection if pagination is true (default)
     */
    public function byPage($paginate = true, $perPage = 25, $api = false)
    {
        // Use sorting and filters, if set
        $this->sort()->filter();

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
     * @return Object object of created model
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
     */
    public function deleteById($id, $force = false)
    {
        if ($force && !is_array($id)) {
            $instance = $this->byId($id);
            $instance->forceDelete();
        }

        $this->model->destroy($id);
    }
}
