<?php


namespace WA\Repositories;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use WA\Services\Validation\ValidableInterface as Validator;

/**
 * Defines all common repository functionality.
 *
 * Class BaseRepository
 *w
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * Reference to the dataStore used for this repository.
     *
     * @var \WA\DataStore\BaseDataStore
     */
    protected $dataStore;

    /**
     * @var Validator
     */
    protected $validator = null;

    /**
     * How the repository should paginate.
     *1.
     *
     * @var false|int
     */
    protected $paginate = false;

    /**
     * If the repository should cache.
     *
     * @var false|int
     */
    protected $cachable = false;

    /**
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * @var int time to cache for
     */
    protected $cacheFor = 30;

    /**
     * Dependency inject the model (and optional validator).
     *
     * @param BaseModel $dataStore
     * @param Validator $validator
     */
    public function __construct(BaseModel $dataStore, Validator $validator = null)
    {
        $this->setDataStore($dataStore);
        if (isset($validator)) {
            $this->setValidator($validator);
        }
    }

    /**
     * Create a new model instance and save it to the database.
     *
     * @param array $attributes optional
     *
     * @return BaseModel
     */
    public function create(array $attributes = [])
    {
        if (isset($this->validator) && !$this->validator->with($attributes)->passes()) {
            return false;
        }

        return $this->dataStore->create($attributes);
    }

    /**
     * Save changes an existing model instance.
     *
     * @param BaseModel $dataStore
     * @param array     $attributes
     *
     * @return bool
     */
    public function update(BaseModel $dataStore, array $attributes)
    {
        if (isset($this->validator) && !$this->validator->with($attributes)->passes()) {
            return false;
        }
        $dataStore->fill($attributes);

        return $dataStore->save();
    }

    /**
     * Delete an existing model instance.
     *
     * @param BaseModel $dataStore
     *
     * @return bool
     */
    public function delete(BaseModel $dataStore = null)
    {
        $dataStore = $dataStore ?: $this->dataStore;

        return $dataStore->delete();
    }

    /**
     * Find by ID.
     *
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->findWhere('id', $id)->first();
    }

    /**
     * @param $columnName
     *
     * @return BaseModel
     */
    public function findWhere($columnName)
    {
        if (count($params = func_get_args()) > 2) {
            return $this->dataStore->where($columnName, $params[1], $params[2]);
        }

        return $this->dataStore->where($columnName, $params[1]);
    }

    /**
     * Toggle pagination. False or no arguments to disable pagination, otherwise
     * provide a number of items to show per page.
     *
     * @param mixed $paginate
     *
     * @return $this
     */
    public function togglePagination($paginate = false)
    {
        if ($paginate === false) {
            $this->paginate = false;
        } else {
            $this->paginate = (int) $paginate;
        }

        return $this;
    }

    /**
     * Get the repository's model.
     *
     * @return BaseModel
     */
    public function getDataStore()
    {
        return $this->dataStore;
    }

    /**
     * Set the repository's model.
     *
     * @param BaseModel $dataStore
     *
     * @return mixed|void
     */
    public function setDataStore(BaseModel $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    /**
     * Fetch the repository validator.
     *
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Set the repository validator.
     *
     * @param Validator $validator
     *
     * @return mixed|void
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Get all the rows from the database.
     *
     * @return mixed
     */
    public function getAll()
    {
        /* @var $query  QueryBuilder builder instance/reference */
        $query = $this->dataStore->newQuery();

        return $this->runQuery($query);
    }

    /**
     * Run a query builder and return its results.
     *
     * @param  $query  QueryBuilder builder instance/reference
     *
     * @return mixed
     */
    protected function runQuery(QueryBuilder $query)
    {
        $this->prepareQuery($query);

        if (is_integer($this->cache)) {
            // $query->remember($this->cache);
        }

        if ($this->paginate === false) {
            return $query->get();
        } else {
            return $query->paginate($this->paginate);
        }
    }

    /**
     * This function is ran by runQuery before fetching the results. Put default
     * behaviours in this function.
     *
     * @param QueryBuilder $query
     */
    protected function prepareQuery(QueryBuilder $query)
    {
    }

    /**
     * Get a single row by its primary key.
     *
     * @param mixed $key
     *
     * @return BaseModel|null
     */
    public function getByKey($key)
    {
        return $this->dataStore->find($key);
    }

    /**
     * Get a new instance of the repository's model.
     *
     * @param array $attributes optional
     *
     * @return BaseModel
     */
    public function getNew(array $attributes = [])
    {
        return $this->dataStore->newInstance($attributes);
    }

    /**
     * Inserts bulk values into the datastore
     * !!does not update/insert timestamps.
     *
     * @param array $values
     *
     * @return mixed
     */
    public function insert(array $values)
    {
        return $this->dataStore->insert($values);
    }

    /**
     * Get a quick-access array of key -> ID.
     *
     * @param string $key
     *
     * @return array
     */
    public function getArray($key = 'name')
    {
        $array = [];
        $collection = $this->dataStore->get(['id', $key])->toArray();
        foreach ($collection as $do) {
            $array[$do['name']] = $do['id'];
        }

        return $array;
    }

    public function findWhereIn($column, array $columns)
    {
        return $this->dataStore->whereIn($column, $columns)->get();
    }

    /**
     * Toggle cache. False or no arguments to disable cache, otherwise
     * provide a number of minutes to cache results.
     *
     * @param mixed $cache
     *
     * @return $this
     */
    public function toggleCache($cache = false)
    {
        if ($cache === false) {
            $this->cache = false;
        } else {
            $this->cache = (int) $cache;
        }

        return $this;
    }

    public function paginate($count)
    {
        return $this->dataStore->paginate($count);
    }

    /**
     * Get the paginated asset.
     *
     * @param int  $page
     * @param int  $limit
     * @param bool $paginate
     *
     * @return Object object of asset objects
     */
    public function byPage($page = 1, $limit = 10, $paginate = true)
    {
        $result = new \StdClass();
        $result->page = $page;
        $result->limit = $limit;
        $result->totalItems = 0;
        $result->items = [];

        $model = $this->dataStore;

        if ($paginate) {
            $model =
                $model->skip($limit * ($page - 1))
                    ->take($limit);

            $result->items = $model->get();
        } else {
            $result->items = $model->get();
        }

        $result->totalItems = $this->totalItemCount();

        return $result;
    }

    /**
     * Get the model object information by its ID.
     *
     * @param $id
     *
     * @return object Object of model information
     */
    public function byId($id)
    {
        $model = $this->dataStore;

        if (is_array($id)) {
            return $model->findWhereIn('id', $id)
                ->get();
        }

        $em = $model->where('id', $id)->first();

        return $em;
    }
}
