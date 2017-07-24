<?php

namespace WA\Repositories\Allocation;

use WA\Repositories\AbstractRepository;

use Log;
use Carbon;

class EloquentAllocation extends AbstractRepository implements AllocationInterface
{
    /**
     * Get current charges of user for current billing month.
     *
     * @param $email
     *
     * @return mixed
     */
    public function getCurrentCharges($email)
    {
        $start =  date('Y-m-d 00:00:00', strtotime('first day of this month'));

        $charges = $this->model->where('User Email', $email)->where('Bill Month', $start)->get();

        return $charges;
    }

    /**
     * Get Allocations Transformer.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return $this->model->getTransformer();
    }

    /**
     * Retrieve the filters for the Model.
     *
     * @param int  $companyId
     *
     * @return Array
     */
    public function addFilterToTheRequest($companyId) {
        $aux['companyId']= (string) $companyId;
        return $aux;
    }

    /**
     * Check if the Model and/or its relationships are related to the Company of the User.
     *
     * @param JSON  $json : The Json request.
     * @param int  $companyId
     *
     * @return Boolean
     */
    public function checkModelAndRelationships($json, $companyId) {
        $ok = true;
        $attributes = $json->data->attributes;

        $user = \WA\DataStore\User\User::find($attributes->userId);
        $ok = $ok && ($user->companyId == $companyId);

        $ok = $ok && ($attributes->companyId == $companyId);

        return $ok;
    }

    /**
     * Add the attributes or the relationships needed.
     *
     * @param $data : The Data request.
     *
     * @return $data: The Data with the minimum relationship needed.
     */
    public function addRelationships($data) {
        return $data;
    }
}
