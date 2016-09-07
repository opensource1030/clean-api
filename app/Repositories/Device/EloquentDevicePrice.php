<?php

namespace WA\Repositories\Device;

use Illuminate\Database\Eloquent\Model;
use Log;
use Schema;
use WA\Repositories\AbstractRepository;
use WA\Repositories\JobStatus\JobStatusInterface as StatusInterface;
use WA\Repositories\Traits\AttributableMethods;

class EloquentDevicePrice extends AbstractRepository implements DevicePriceInterface
{
    use AttributableMethods;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var StatusInterface
     */
    protected $status;

    /**
     * @param Model $model
     * @param StatusInterface $status
     */
    public function __construct(Model $model, StatusInterface $status)
    {
        $this->model = $model;
        $this->status = $status;
    }

    /**
     * Get the model's transformation.
     */
    public function getTransformer()
    {
        return $this->model->getTransformer();
    }

    /**
     * Update a repository.
     *
     * @param array $data to be updated
     *
     * @return Object object of updated repo
     */
    public function update(array $data)
    {
        return $this->model->update($data);
    }
}
