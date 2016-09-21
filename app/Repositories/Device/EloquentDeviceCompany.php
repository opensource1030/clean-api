<?php

namespace WA\Repositories\Device;

use Illuminate\Database\Eloquent\Model;
use Log;
use Schema;
use WA\Repositories\AbstractRepository;
use WA\Repositories\JobStatus\JobStatusInterface as StatusInterface;
use WA\Repositories\Traits\AttributableMethods;

class EloquentDeviceCompany extends AbstractRepository implements DeviceCompanyInterface
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
}
