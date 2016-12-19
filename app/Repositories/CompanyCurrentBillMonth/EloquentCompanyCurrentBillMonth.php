<?php

namespace WA\Repositories\CompanyCurrentBillMonth;

use Illuminate\Database\Eloquent\Model;
use WA\Repositories\AbstractRepository;

class EloquentCompanyCurrentBillMonth extends AbstractRepository implements  CompanyCurrentBillMonthInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;


    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * CurrentBillMonth by the id.
     *
     * @param int $id
     *
     * @return object object of the CurrentBillMonth information
     */
    public function byId($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Get CurrentBillMonths Transformer.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return $this->model->getTransformer();
    }


}