<?php


namespace WA\DataStore\Allocation;

use WA\DataStore\Allocation\AllocationTransformer;
use WA\DataStore\BaseDataStore;


/**
 * Class Allocations
 *
 * @package WA\DataStore
 * @property-read \WA\DataStore\Company\Company $companies
 * @property-read \WA\DataStore\Employee\Employee $employees
 * @mixin \Eloquent
 */
class Allocation extends BaseDataStore
{
    protected $table = 'allocations';

    public $timestamps = false;


    public function companies()
    {
        return $this->belongsTo('WA\DataStore\Company\Company', 'companyId');
    }

    public function employees()
    {
        return $this->belongsTo('WA\DataStore\Employee\Employee', 'employeeId');
    }

    /**
     * Get the transformer instance.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return new AllocationTransformer();
    }


}