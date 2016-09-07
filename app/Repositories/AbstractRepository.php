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

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get paginated census.
     *
     * @param int  $perPage
     * @param bool $api      false|true
     * @param bool $paginate
     *
     * @return Object as Collection of object information, | Paginator Collection if pagination is true (default)
     */
    public function byPage($paginate = true, $perPage = 25, $api = false)
    {
        if (!$paginate) {
            return $this->model->get();
        }

        return $this->model->paginate($perPage);
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
        return $this->model->create($data);
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
     * @param int  $id
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
