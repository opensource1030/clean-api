<?php

namespace WA\Repositories;

use Illuminate\Database\Eloquent\Model as BaseModel;
use WA\Services\Validation\ValidableInterface as Validator;

/**
 * Defines all common repository functionality.
 *
 * Class BaseRepository
 *w
 */
interface BaseRepositoryInterface
{
    /**
     * Create a new model instance and save it to the database.
     *
     * @param array $attributes optional
     *
     * @return BaseModel
     */
    public function create(array $attributes = []);

    /**
     * Save changes an existing model instance.
     *
     * @param BaseModel $dataStore
     * @param array     $attributes
     *
     * @return bool
     */
    public function update(BaseModel $dataStore, array $attributes);

    /**
     * Delete an existing model instance.
     *
     * @param BaseModel $dataStore
     *
     * @return bool
     */
    public function delete(BaseModel $dataStore = null);

    /**
     * Find by ID.
     *
     * @param $id
     *
     * @return mixed
     */
    public function find($id);

    /**
     * @param $columnName
     *
     * @return BaseModel
     */
    public function findWhere($columnName);

    /**
     * Toggle pagination. False or no arguments to disable pagination, otherwise
     * provide a number of items to show per page.
     *
     * @param mixed $paginate
     *
     * @return $this
     */
    public function togglePagination($paginate = false);

    /**
     * Get the repository's model.
     *
     * @return BaseModel
     */
    public function getDataStore();

    /**
     * Set the repository's model.
     *
     * @param BaseModel $dataStore
     *
     * @return mixed|void
     */
    public function setDataStore(BaseModel $dataStore);

    /**
     * Fetch the repository validator.
     *
     * @return Validator
     */
    public function getValidator();

    /**
     * Set the repository validator.
     *
     * @param Validator $validator
     *
     * @return mixed|void
     */
    public function setValidator(Validator $validator);

    /**
     * Get all the rows from the database.
     *
     * @return mixed
     */
    public function getAll();

    /**
     * Get a single row by its primary key.
     *
     * @param mixed $key
     *
     * @return BaseModel|null
     */
    public function getByKey($key);

    /**
     * Get a new instance of the repository's model.
     *
     * @param array $attributes optional
     *
     * @return BaseModel
     */
    public function getNew(array $attributes = []);

    /**
     * Inserts bulk values into the datastore
     * !!does not update/insert timestamps.
     *
     * @param array $values
     *
     * @return mixed
     */
    public function insert(array $values);

    /**
     * Get a quick-access array of key -> ID.
     *
     * @param string $key
     *
     * @return array
     */
    public function getArray($key = 'name');

    public function findWhereIn($column, array $columns);

    /**
     * Toggle cache. False or no arguments to disable cache, otherwise
     * provide a number of minutes to cache results.
     *
     * @param mixed $cache
     *
     * @return $this
     */
    public function toggleCache($cache = false);

    public function paginate($count);

    /**
     * Get the paginated asset.
     *
     * @param int  $page
     * @param int  $limit
     * @param bool $paginate
     *
     * @return Object object of asset objects
     */
    public function byPage($page = 1, $limit = 10, $paginate = true);

    /**
     * Get the model object information by its ID.
     *
     * @param $id
     *
     * @return object Object of model information
     */
    public function byId($id);
}
